<?php

class App_Expert_Peepso_Friends_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/friends', "App_Expert_Peepso_Friends_List_Endpoint@get" , $this->get_friends_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/friends/requests', "App_Expert_Peepso_Friends_List_Endpoint@get_requests" ,  $this->get_requests_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/friends/action/(?P<friend_action>\D+)', "App_Expert_Peepso_Friends_List_Endpoint@actions" , $this->get_friends_actions_parameters(),"App_Expert_Auth_Request");

    }
    public function get_friends_parameters(){
        return [
            'user_id'        => array(
                'description' => __( 'get friends list of which user.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'page'        => array(
                'description' => __( 'page number.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'limit'        => array(
                'description' => __( 'number of friends per page.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
        ];
    }
    public function get_requests_parameters(){
        return [
            'type'        => array(
                'description' => __( 'get friends requests lists sent/received.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
        ];
    }
    public function get_friends_actions_parameters(){
        return [
            'friend_action'        => array(
                'description' => __( 'one of (add_friend,cancel_request,accept_request,remove_friend).' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'user_id'        => array(
                'description' => __( 'other user that the action will be taken on.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'request_id'        => array(
                'description' => __( 'required if action is cancel_request,accept_request' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'action'        => array(
                'description' => __( "required if action is cancel_request (allowed values:['remove'(cancel a request i sent),'ignore'(cancel a request i received)], default:'remove') " , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
        ];
    }
}

new App_Expert_Peepso_Friends_Routes();

