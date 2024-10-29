<?php

class App_Expert_Peepso_Core_Users_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes(){
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/users/profile(?:/(?P<id>\d+))?', "App_Expert_Peepso_Core_Users_Endpoint@get_user" , $this->get_user_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/users/followers', "App_Expert_Peepso_Core_Users_Endpoint@get_followers" , $this->get_follow_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/users/followings', "App_Expert_Peepso_Core_Users_Endpoint@get_following" , $this->get_follow_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/users/profile-image', "App_Expert_Peepso_Core_Users_Endpoint@edit_profile_image" , $this->get_profile_image_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/users/cover-imag', "App_Expert_Peepso_Core_Users_Endpoint@edit_coverImage" , $this->get_cover_image_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/users/report', "App_Expert_Peepso_Core_Users_Endpoint@report" , $this->get_report_parameters(), "App_Expert_Auth_Request");
    }

    private function get_user_parameters()
    {
        return [
            'id'        => array(
                'description' => __( 'user id & default value is current user' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
        ];
    }

    private function get_profile_image_parameters()
    {
        return [
            'profile_image'        => array(
                'description' => __( 'image file' , 'app-expert' ),
                'type'        => 'file',
                //todo:do ur own validation file is not working with required types
                'required' => false
            ),
        ];
    }

    private function get_cover_image_parameters()
    {
        return [
            'cover_image'        => array(
                'description' => __( 'image file' , 'app-expert' ),
                'type'        => 'file',
                //todo:do ur own validation file is not working with required types
                'required' => false
            ),
        ];
    }

    public function get_report_parameters()
    {
        return  array(
            'user_id'        => array(
                'description' => __( 'user id to report.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'reason'        => array(
                'description' => __( 'one of reasons in settings.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'reason_desc'        => array(
                'description' => __( 'Report description.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
        );
    }

    private function get_follow_parameters()
    {
        return  array(
            'user_id'        => array(
                'description' => __( 'user id to report.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'page'        => array(
                'description' => __( 'one of reasons in settings.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'limit'        => array(
                'description' => __( 'Report description.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
        );
    }


}

new App_Expert_Peepso_Core_Users_Routes();

