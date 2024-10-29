<?php
class App_Expert_User_Response_Helper {

    public static function jwt_response($user,$request){
        $app_expert_jwt=new App_Expert_JWT_Helper();
        $jwt_token =  $app_expert_jwt->get_jwt_token( $user );


        if ( is_a( $jwt_token, 'Exception' ) ) {
            return new WP_Error(
                $jwt_token->getMessage(),
                __( 'Something happened while getting the token', 'app-expert' ),
                array(
                    'status' => 500,
                )
            );
        }


        return self::get_response($user , $jwt_token,$request);

    }
    private static function get_response(WP_User $user, array  $token, WP_REST_Request $request){

        $user_data = App_Expert_User_Response_Helper::prepare_user_data_for_response($user);

        $user_profile_completed = get_user_meta($user->ID, 'is_profile_completed',true);

        $response = New WP_REST_Response();
        if($user_profile_completed){
            $user_data['is_profile_completed'] = $user_profile_completed[0];
        }
        $filer_data=apply_filters('ae_login_user_data_object',$user_data,$request,$user);
        $res_data = array_merge($token , $user_data,$filer_data);
        $response->set_data($res_data);

        $response = rest_ensure_response($response);
        if (is_wp_error($response))
            return $response;

        return App_Expert_Response::wp_rest_success($response,"app_expert_retieved_token","retrieved Token");
    }

    private static function get_user_wp_fields()
    {
        return array(
            'id' => 'id',
            'user_email' => 'email',
            'first_name' => 'first_name',
            'last_name' => 'last_name',
            'avatar_urls' => 'image'
        );
    }
    public static function prepare_user_data_for_response(WP_User $user)
    {
        $fields = self::get_user_wp_fields();

        $data = array();
        if (isset($fields['id'])) {
            $data[$fields['id']] = $user->ID;
        }

        if (isset($fields['user_email'])) {
            $data[$fields['user_email']] = $user->user_email;
        }

        if (isset($fields['first_name'])) {
            $data[$fields['first_name']] = $user->first_name;
        }

        if (isset($fields['last_name'])) {
            $data[$fields['last_name']] = $user->last_name;
        }

        if (isset($fields['avatar_urls'])) {
            $data[$fields['avatar_urls']] = array(
                'small' => get_avatar_url($user, array('size' => USER_IMAGE_SMALL)),
                'medium' => get_avatar_url($user, array('size' => USER_IMAGE_MEDIUM)),
                'large' =>  get_avatar_url($user, array('size' => USER_IMAGE_LARGE)),
            );
        }
        $data['phone'] = get_user_meta( $user->ID, 'user_phone',true);
        $data['phone'] = !empty($data['phone'])?$data['phone']:null;

        $user_profile_completed = get_user_meta($user->ID, 'is_profile_completed',true);
        if(strlen($user_profile_completed)<=0) $user_profile_completed ="1";
        $data['is_profile_completed'] =  (boolean)  $user_profile_completed;

        $user_logged_in_by_mobile = (boolean) get_user_meta($user->ID,'login_by_firebase', true);
        $data['login_type'] = $user_logged_in_by_mobile?"phone_otp":"user_password";

        $data=apply_filters('ae_login_user_data_object',$data,new WP_REST_Request(),$user);

        return $data;
    }

}