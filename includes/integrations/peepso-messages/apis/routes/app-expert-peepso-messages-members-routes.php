<?php

class App_Expert_Peepso_Messages_Members_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/conversations/add-new-recipients', "App_Expert_Peepso_Messages_Members_Endpoint@add_new_recipients_to_conversation" , $this->get_add_new_recipients_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/conversations/available-recipients', "App_Expert_Peepso_Messages_Members_Endpoint@get_available_recipients" , $this->get_available_recipients_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/conversations/list-members', "App_Expert_Peepso_Messages_Members_Endpoint@get_chat_members_list" , $this->get_chat_members_list_parameters(),"App_Expert_Auth_Request");
    }
    public function get_available_recipients_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'keyword'        => array(
                'description' => __( 'keyward to search with.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        ];
    }
    
    public function get_add_new_recipients_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'recipients'        => array(
                'description' => __( 'array of recipients ids like [2,3].' , 'app-expert' ),
                'type'        => 'array',
                'required' => true
            ),
        ];
    }
    public function get_chat_members_list_parameters(){
        return [
            'msg_parent_id'        => array(
                'description' => __( 'conversation parent id (msg_parent_id param in conversation list).' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
        ];
    }
}

new App_Expert_Peepso_Messages_Members_Routes();

