<?php

class App_Expert_Peepso_Messages_Notifications_Routes
{
    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/conversations/messages/typing-notification', "App_Expert_Peepso_Messages_Notifications_Endpoint@send_typing_notification" , $this->get_typing_notification_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/conversations/read-notification', "App_Expert_Peepso_Messages_Notifications_Endpoint@set_message_read_notification" , $this->get_read_notification_parameters(),"App_Expert_Auth_Request");
    }
   
    public function get_typing_notification_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'is_typing'        => array(
                'description' => __('flag is 1 when user is typing and 0 when user stop typing a message.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            )
        ];
    }
    
    public function get_read_notification_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'read_notif'        => array(
                'description' => __( 'flag is 1 when choose to send read receipt and 0 if dont send' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
        ];
    }
}

new App_Expert_Peepso_Messages_Notifications_Routes();

