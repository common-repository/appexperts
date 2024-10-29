<?php

class App_Expert_Notification_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes(){
        //get notification
        App_Expert_Route::get(APP_EXPERT_API_NAMESPACE, '/notification'                 , "App_Expert_Notification_Endpoint@get_items"            , $this->get_notification_parameters(),"App_Expert_Auth_Request");
        //mark as seen
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/notification/mark_as_seen'   , "App_Expert_Notification_Endpoint@mark_as_seen"         , $this->get_mark_as_seen_notification_parameters(),"App_Expert_Auth_Request");

        //handle notification token
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/add_token'                    , "App_Expert_Notification_Token_Endpoint@handle_register_token"           , $this->get_register_token_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/remove_token'                , "App_Expert_Notification_Token_Endpoint@handle_unregister_token"         , $this->get_register_token_parameters(),"App_Expert_Auth_Request");
    }

    public function get_notification_parameters(){
        return  array(
            'page'        => array(
                'description' => __( 'page' , 'app-expert' ),
                'type'        => 'int',
                'context'     => array( 'edit' ),
                'required' => false
            ),
            'per_page'        => array(
                'description' => __( 'count of items per page' , 'app-expert' ),
                'type'        => 'int',
                'context'     => array( 'edit' ),
                'required' => false
            )
        );
    }

    public function get_mark_as_seen_notification_parameters()
    {
        return  array(
            'notification_id'        => array(
                'description' => __( 'notification to mark as seen' , 'app-expert' ),
                'type'        => 'int',
                'context'     => array( 'edit' ),
                'required' => false
            )
        );
    }

    public function get_register_token_parameters(){
        return [
                'token'       => array(
                'description' => __( 'notification token' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'edit' ),
                'required'    => true
            ),
        ];
    }
}

new App_Expert_Notification_Routes();

