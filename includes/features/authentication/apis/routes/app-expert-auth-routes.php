<?php
class App_Expert_Auth_Routes {
    public function __construct(){
        $this->_register_route();
    }

    public function _register_route() {
        //normal login
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/login'           , "App_Expert_Login_Endpoint@login"                     , $this->get_login_parameters());
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/refresh_token'   , 'App_Expert_Login_Endpoint@refresh_token'             , $this->get_refresh_token_parameters());

        //register
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/register'         , "App_Expert_Register_Endpoint@create_item"           , $this->get_register_parameters(),"App_Expert_Register_Request");

        //phone login
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/phone-check'     , "App_Expert_Phone_Login_Endpoint@check_user_phone"    , $this->get_check_phone_parameters());
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/login-by-mobile' , "App_Expert_Phone_Login_Endpoint@login_by_mobile"     , $this->get_login_by_mobile_parameters());

        //forget password
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/users/forgot_password', "App_Expert_Forgot_Password_Endpoint@forgot_password"    , $this->get_forget_password_parameters(),"App_Expert_Forget_Password_Request");

    }

    public function get_login_parameters(){
        return  apply_filters('ae_login_endpoint_params',array(
            'username'        => array(
                'description' => __( 'Login name for the user.' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'edit' ),
                'required' => true
            ),
            'password'           => array(
                'description' => __( 'Login name for the user.' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array(  ),
                'required'    => true,
            ),
        ));
    }

    public function get_register_parameters(){
        return array(
           'username'              => array(
               'description' => __('Login name for the user.'),
               'type'        => 'string',
               'context'     => array('edit'),
           ),
           'first_name'            => array(
               'description' => __('First name for the user.'),
               'type'        => 'string',
               'context'     => array('edit'),
               'required' => true
           ),
           'last_name'             => array(
               'description' => __('Last name for the user.'),
               'type'        => 'string',
               'context'     => array('edit'),
               'required' => true
           ),
           'email'                 => array(
               'description' => __('The email address for the user.'),
               'type'        => 'string',
               'format'      => 'email',
               'context'     => array('edit'),
               'required'    => true,
           ),
           'password'              => array(
               'description' => __('Password for the user (never included).'),
               'type'        => 'string',
               'context'     => array(),
               'required'    => true,
           ),
           'password_confirmation' => array(
                       'description' => __('Password Confirmation  for the user (never included).'),
                       'type'        => 'string',
                       'context'     => array(),
                       'required'    => true,
                   )
        );
    }

    public function get_refresh_token_parameters(){
        return  array(
            'refresh_token'        => array(
                'description' => __( 'The Refresh Token.' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'edit' ),
                'required' => true
            ),
        );
    }

    public function get_check_phone_parameters(){
        return  array(
            'user_phone'        => array(
                'description' => __( 'User Phone' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'edit' ),
                'required' => true
            )
        );
    }

    public function get_login_by_mobile_parameters(){
        return apply_filters('ae_phone_login_endpoint_params', array(
            'firebase_access_token'        => array(
                'description' => __( 'Firebase Access Token' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'edit' ),
                'required'    => true
            )
        ));
    }

    public function get_forget_password_parameters(){
        return  array(
            'email'           => array(
                'description' => __( 'Forgot Password User Email' , 'app-expert' ),
                'type'        => 'string',
                'format'      => 'email',
                'context'     => array( 'edit' ),
                'required'    => true,
            )
        );
    }

}
new App_Expert_Auth_Routes();