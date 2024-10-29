<?php

Class App_Expert_Auth_Modification{
    public $_lang;
    public function __construct()
    {
        add_filter('ae_login_endpoint_params' , array($this, 'add_token_param'), 10, 1);
        add_filter('ae_phone_login_endpoint_params' , array($this, 'add_token_param'), 10, 1);
        add_filter('ae_login_user_data_object', array($this, 'add_auth_changes'), 10, 3);
    }

    public function add_token_param($params){
        $params['token']      = array(
                'description' => __( 'notification token' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array(  ),
                'required'    => false,
            );
        return $params;
    }

    public function add_auth_changes($user_data,WP_REST_Request $request,WP_User $user){
        $push_token = $request->get_param('token');
        if(!empty($push_token)){
           $user_id=$user->ID;
            $tokens = get_user_meta($user_id,NOTIFICATION_TOKEN_META,true);

            if(!is_array($tokens)) $tokens=[];
            if(!in_array($push_token,array_values($tokens))) $tokens[]=$push_token;
            update_user_meta($user_id,NOTIFICATION_TOKEN_META,$tokens);
            //add user role to topics
            $user_roles = $user->roles;
            foreach ($user_roles as $role ){
                App_Expert_FCM_Helper::set_topics($role,$tokens);
            }
            $this->send_welcome_message($user);
        }

        $user_data['unseenCount'] = App_Expert_Notification_Helper::get_unseen_count($user);
        return $user_data;
    }
    public function send_welcome_message($userObj){
        $user_id = $userObj->ID;
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)) return;
        $is_option_exist = array_key_exists('user_signup',$notification_options['general_settings']);
        if(!$is_option_exist) return;

        $is_sent=get_user_meta($user_id,"_ae_register_push",true);
        if($is_sent)  return;

        $_user_token   = get_user_meta($user_id,'_as_notification_tokens',true);
        if(!$_user_token) {
            update_user_meta($user_id,"_ae_register_push",0);
        }else{
            App_Expert_Notification_Helper::save_automatic(
                'Welcome',
                'Thank you for signing up',
                "user",$user_id,$user_id);
            update_user_meta($user_id,"_ae_register_push",1);
        }

    }
}
new App_Expert_Auth_Modification();