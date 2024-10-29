<?php
class App_Expert_Login_Endpoint {

    private $app_expert_jwt;

    public function __construct() {
        $this->app_expert_jwt = new App_Expert_JWT_Helper();
    }


    public function login( WP_REST_Request $request ) {


        $username = $request->get_param( 'username' );
        $password = $request->get_param( 'password' );

        if ( empty( $username ) || empty( $password ) ) {
            return new WP_Error(
                'app_expert_username_password_not_present',
                __( 'Email or password parameter is not provided', 'app-expert' ),
                array(
                    'status' => 400,
                )
            );
        }

        $user = wp_authenticate( $username, $password );

        if ( is_wp_error( $user ) ) {
            return apply_filters('ae_authentication_fail', new WP_Error(
                'app_expert_error_authenticating_user',
                __( 'Wrong email or password', 'app-expert' ),
                array(
                    'status' => 401,
                )
            ), $user);
        }

        return App_Expert_User_Response_Helper::jwt_response($user,$request);
    }

    public function refresh_token( WP_REST_Request $request ) {
        $refresh_token = $request->get_param( 'refresh_token' );

        if (empty( $refresh_token )) {
            return new WP_Error(
                'app_expert_no_refresh_token_error',
                __( 'refresh_token parameter is not provided', 'app-expert' ),
                array(
                    'status' => 400,
                )
            );
        }


        $refresh_token_decoded = $this->app_expert_jwt->decode_refresh_token( $refresh_token );
        if ( is_a( $refresh_token_decoded, 'Exception' ) ) {
            return new WP_Error(
                $refresh_token_decoded->getMessage(),
                __( 'Something happened while decoding the token', 'app-expert' ),
                array(
                    'status' => 401,
                )
            );
        }

        if ( ! $this->app_expert_jwt->is_refresh_token_valid( $refresh_token ) ) {
            return new WP_Error(
                "app_expert_user_no_refresh_token_or_don't_match_current_token",
                __( "This user doesn't have a refresh token or the token doesn't match the current token.", 'app-expert' ),
                array(
                    'status' => 401,
                )
            );
        }
        $user = $this->app_expert_jwt->get_user_by_refresh_token($refresh_token)[0];
        $old_token = get_user_meta($user->ID, 'app_expert_refresh_token', true);
        if (($key = array_search($refresh_token, $old_token)) !== false) {
            unset($old_token[$key]);
        }
        update_user_meta($user->ID, 'app_expert_refresh_token', $old_token);

        return App_Expert_User_Response_Helper::jwt_response($user,$request);

    }

}