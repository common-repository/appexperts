<?php

class App_Expert_Peepso_Messages_Conversations_Routes
{
    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/conversations', "App_Expert_Peepso_Messages_Conversations_Endpoint@get" , $this->get_chats_parameters(),"App_Expert_Auth_Request");
    }

    public function get_chats_parameters(){
        return [
            'page'        => array(
                'description' => __( 'page number.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'per_page'        => array(
                'description' => __( 'number of chats per page.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'unread_only'        => array(
                'description' => __( 'get read-only conversations by sending 1.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'search'        => array(
                'description' => __( 'text to search chat messages.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        ];
    }
}

new App_Expert_Peepso_Messages_Conversations_Routes();

