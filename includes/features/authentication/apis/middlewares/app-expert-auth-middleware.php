<?php
class App_Expert_Auth_Middleware {
    public function __construct(){
        add_filter('authenticate', array($this, 'authenticate_api'),1,3);
        add_filter('determine_current_user', array($this, 'determine_current_user'),10,1);
    }

    public function authenticate_api($user, $username, $password){

        if ( $user instanceof WP_User ) return $user;
        $user_id =  App_Expert_Api_jwt_Token_Helper::get_user_from_token();
        if(isset($_REQUEST['wpml_language'])){
            update_user_meta($user_id,'_as_current_lang',$_REQUEST['wpml_language']);
        }        
        return get_user_by('ID',$user_id);
    }
    public function determine_current_user($user_id){
        if ( !empty($user_id) ) return $user_id;
        $user_id =   App_Expert_Api_jwt_Token_Helper::get_user_from_token();
        if(isset($_REQUEST['wpml_language'])){
            update_user_meta($user_id,'_as_current_lang',$_REQUEST['wpml_language']);
        }
        return $user_id;
    }
}