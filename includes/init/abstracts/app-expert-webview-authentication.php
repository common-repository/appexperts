<?php
abstract class App_Expert_Webview_Authentication
{
    public function __construct(){
        add_action('init', array($this, 'authenticate_user'));
    }


    abstract public function isAllowed();


    //todo: move part related to woocommerce in woo commerce
    public function authenticate_user(){

        if (!$this->isAllowed()||get_current_user_id()) {
            return;
        }

        $request_uri = $_SERVER['REQUEST_URI'];
        // Guest user case
        if (isset($_GET['guest_user']) && !empty($_GET['guest_user'])) {  // Guest case
            if(
                ( isset($_GET['session_key'])   && !empty($_GET['session_key']) )&&
                !isset($_COOKIE[$_GET['session_key']]) &&
                ( isset($_GET['session_value']) && !empty($_GET['session_value']) )
            ){
                $session_value=$_GET['session_value'];
                $session_value=str_replace('%257C','|',$session_value);
                $session_value=str_replace('%7C','|',$session_value);
                setcookie($_GET['session_key'],$session_value,0,"/");
                wp_redirect($request_uri);
                exit;
            }
            if (!empty(get_current_user_id())) {
                $this->clear_current_user();
                wp_redirect($request_uri);
                exit;
            }
            return;
        }

        // Authenticated user case
        $user_id = 0;
        //Try from Url param
        if (isset($_GET['user_token']) && !empty($_GET['user_token'])) {
            $authorization_header = "Bearer " . $_GET['user_token'];
            $token_auth_result = App_Expert_Api_jwt_Token_Helper::authenticate_user_token($authorization_header);
            if (!is_wp_error($token_auth_result)) {
                $user_id = $token_auth_result;
            }
        }
        //try from header
        else{
            $user_id = App_Expert_Api_jwt_Token_Helper::get_user_from_token();
        }


        if ($user_id) {
            $user = get_user_by('ID', $user_id);
            if(!$user)return;
            $this->open_user_session($user);
            wp_redirect($_SERVER['REQUEST_URI']);
            exit;
        }
    }


    protected function clear_current_user()
    {
        wp_clear_auth_cookie();
        wp_destroy_current_session();
    }

    protected function open_user_session($user)
    {

        clean_user_cache($user->ID);
        $this->clear_current_user();

        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID, true, false);
        update_user_caches($user);
    }

}