<?php

class App_Expert_Peepso_Messages_Actions_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/conversations/leave', "App_Expert_Peepso_Messages_Actions_Endpoint@leave_conversation" , $this->get_leave_conversation_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/conversations/mute', "App_Expert_Peepso_Messages_Actions_Endpoint@mute_conversation" , $this->get_mute_conversation_parameters(),"App_Expert_Auth_Request");
    }
    public function get_leave_conversation_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
        ];
    }
    public function get_mute_conversation_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'mute_period'        => array(
                'description' => __( 'mute period in hours. day -> 24 , week ->168 ,  until I unmute it -> 9999,unmute -> 0. ' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
        ];
    }
}

new App_Expert_Peepso_Messages_Actions_Routes();

