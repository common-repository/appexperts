<?php
class App_Expert_User_Routes  extends  WP_REST_Controller{
    public function __construct(){
        $this->_register_routes();
    }

    public function _register_routes() {

        App_Expert_Route::get(APP_EXPERT_API_NAMESPACE, '/users/me', "App_Expert_User_Endpoint@get_current_item", [], "App_Expert_Auth_Request");

        App_Expert_Route::put(APP_EXPERT_API_NAMESPACE, '/users/me', "App_Expert_User_Endpoint@update_current_item", $this->get_update_profile_param(), "App_Expert_Edit_Profile_Request");
        App_Expert_Route::delete(APP_EXPERT_API_NAMESPACE, '/users/me/delete', "App_Expert_User_Endpoint@delete_user",[], "App_Expert_Auth_Request");

        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/users/me/change_password', "App_Expert_Change_Password_Endpoint@change_password", $this->get_change_password_schema(), "App_Expert_Change_Password_Request");

    }

    public function get_change_password_schema() {
        return array(
                'current_password' => array(
                    'description' => __( 'Current Password for the user (never included).'  , 'app-expert'),
                    'type'        => 'string',
                    'context'     => array(),
                    'required'    => true,
                ),
                'new_password' => array(
                    'description' => __( 'New Password for the user (never included).'  , 'app-expert'),
                    'type'        => 'string',
                    'context'     => array(),
                    'required'    => true,
                ),
                'password_confirmation' => array(
                    'description' => __( 'Confirmation Password for the user (never included).'  , 'app-expert'),
                    'type'        => 'string',
                    'context'     => array(),
                    'required'    => true,
                ),
        );
    }

    public function get_update_profile_param(){
        return [
            'first_name'                   => array(
                'description' => __('First name for the user.', 'app-expert'),
                'type'        => 'string',
                'context'     => array('edit'),
            ),
            'last_name'                    => array(
                'description' => __('Last name for the user.', 'app-expert'),
                'type'        => 'string',
                'context'     => array('edit'),
            ),
            'email'                        => array(
                'description' => __('The email address for the user.', 'app-expert'),
                'type'        => 'string',
                'format'      => 'email',
                'context'     => array('edit'),
            ),
            'firebase_access_token'        => array(
                'description' => __( 'Firebase Access Token' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'edit' ),
            ),
            'phone'        => array(
                'description' => __( 'User phone' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'edit' ),
            )
        ];
    }
}
new App_Expert_User_Routes();