<?php

class App_Expert_Forgot_Password_Endpoint {
    public function forgot_password(WP_REST_Request $request){
        $email = $request->get_param('email');
        $user = get_user_by_email($email);

        if (is_wp_error($user))
            return $user;

        $user_login = $user->user_login;

        $key  = get_password_reset_key( $user);

        if (is_wp_error($key))
            return $key;

        if ( is_multisite() ) {
            $site_name = get_network()->site_name;
        } else {
            $site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
        }

        $message  = __( 'Someone has requested a password reset for the following account:' , 'app-expert' ) . "\r\n\r\n";
        $message .= sprintf( __( 'Site Name: %s' ,  'app-expert' ), $site_name ) . "\r\n\r\n";
        $message .= sprintf( __( 'Username: %s' ,  'app-expert' ), $user_login ) . "\r\n\r\n";
        $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ,  'app-expert' ) . "\r\n\r\n";
        $message .= __( 'To reset your password, visit the following address:' ,  'app-expert' ) . "\r\n\r\n";
        $message .= '<' . network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' ) . ">\r\n";

        $title = sprintf( __( '[%s] Password Reset' , 'app-expert' ), $site_name );

        if ( $message && ! wp_mail( $email, wp_specialchars_decode( $title ), $message ) ) {
            return new WP_Error(
                "app_expert_password_reset_send_error",
                sprintf(
                /* translators: %s: Documentation URL. */
                    __( '<strong>ERROR</strong>: The email could not be sent. Your site may not be correctly configured to send emails. <a href="%s">Get support for resetting your password</a>.', 'app-expert' ),
                    esc_url( __( 'https://wordpress.org/support/article/resetting-your-password/' ) )
                ),
                array(
                    'status' => 502,
                )
            );
        }

        return array(
            'code' => 'app_expert_updated_user',
            'message' => __("An Email has been sent. Please check your email for the recovery email to reset your password. If you don't get an email check your spam folder or try again." , 'app-expert'),
            'data' => array(
                'status' => 200
            )
        );
    }
}