<?php

class App_Expert_Peepso_Messages_Messages_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/conversations/messages', "App_Expert_Peepso_Messages_Messages_Endpoint@get_messages" , $this->get_messages_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/conversations/messages', "App_Expert_Peepso_Messages_Messages_Endpoint@create_new_message" , $this->get_new_messages_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/conversations/messages/mark-read', "App_Expert_Peepso_Messages_Messages_Endpoint@mark_read_messages_in_conversation" , $this->mark_message_read_parameters(),"App_Expert_Auth_Request");
    }
    
    public function get_messages_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'from_id'        => array(
                'description' => __( 'start from message id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'direction'        => array(
                'description' => __( 'direction of chat scroll (new,old).' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'get_participants'        => array(
                'description' => __( 'get all participants in conversation (1,0)' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'get_unread'        => array(
                'description' => __( 'to get unread details(1,0). note* 3 param returned 1-unread num of un read messages for all users 
                2-receipt false for users when one of members in chat choose not send receipt 3-send_receipt is 0 when user choose dont send read receipt from chat option ..to mark message as seen need the unread flag to be 0 and receipt true and send_receipt 1 ' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'get_options'        => array(
                'description' => __( 'get chat options (1,0)' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
        ];
    }
    
    public function mark_message_read_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )
        ];
    }
    
    public function get_new_messages_parameters(){
        return [
            'message'        => array(
                'description' => __( 'the message content.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'recipients'        => array(
                'description' => __( 'array of recipients ids like [2,3].' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            ),
            'msg_parent_id'        => array(
                'description' => __( 'required if not new conv ..conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'mood'        => array(
                'description' => __( 'mood id .' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'type'        => array(
                'description' => __( 'giphy if gif is sent or photo if send photo .' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'giphy'        => array(
                'description' => __( 'gif url.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'files'        => array(
                'description' => __( 'array of images file names after upload photos using posts/upload_photo endpoint.' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            )
        ];
    }
}

new App_Expert_Peepso_Messages_Messages_Routes();

