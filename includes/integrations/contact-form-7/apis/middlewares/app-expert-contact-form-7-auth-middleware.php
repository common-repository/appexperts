<?php
class App_Expert_Contact_Form_7_Auth_Middleware {
    private $regex = "/\/contact-form-7\/v1\/contact-forms\/[0-9]+\/feedback$/";

    public function __construct(){
        add_filter( 'rest_request_before_callbacks', array($this, 'create_guest_user'), 10, 3 );
        add_filter( 'wpcf7_skip_spam_check', array($this, 'disable_check'), 10, 1 );
    }
    function create_guest_user( $response, $handler, WP_REST_Request $request ) {
        if(str_contains($request->get_route(),'contact-form-7')){
            if($request->get_header('authorization'))
            {
                $user_id=App_Expert_Api_jwt_Token_Helper::get_user_from_token();
                $user=get_user_by('ID',$user_id);
            }
            else
            {
                $user=get_user_by('login','guest_user');
                if(!$user)
                {
                    $user_id=wp_create_user('guest_user',random_bytes(10),'guest_user@app-experts.io');
                    $user=get_user_by('ID',$user_id);
                }
            }
            add_role( 'form_role', 'form7 role',array(
                'edit_posts'=>true,
                'publish_pages'=>true
            ));

            $user->add_role('form_role');
            wp_set_current_user($user->ID);
        }

        return $response;
    }
    function disable_check($check){
        $matches = [];
        $current_route = $_SERVER["REQUEST_URI"];
        $current_route = explode("?",$current_route);
        $current_route = $current_route[0];
        preg_match($this->regex,$current_route,$matches);
        if(count($matches)){
            return true;
        }
        return  $check;
    }
}