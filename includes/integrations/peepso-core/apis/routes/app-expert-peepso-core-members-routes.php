<?php

class App_Expert_Peepso_Core_Members_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/members', "App_Expert_Peepso_Core_Members_Endpoint@search_members" , $this->get_search_parameters(), "App_Expert_Auth_Request");
        //follow
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/members/action/set-follow', "App_Expert_Peepso_Core_Members_Endpoint@follow" , $this->get_follow_parameters(), "App_Expert_Auth_Request");

    }

    public function get_follow_parameters(){
        return [
            'user_id'        => array(
                'description' => __( 'other user that the action will be taken on.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'follow'        => array(
                'description' => __( 'required if action is follow (0 (un-follow),1 (follow))' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            )
        ];
    }

    private function get_search_parameters()
    {
        return [
            'limit'        => array(
                'description' => __( 'number of posts.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'page'        => array(
                'description' => __( 'page number.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'order_by'        => array(
                'description' => __( 'default (Alphabetical) and if u need to change it u have these 3 options peepso_last_activity(Recently online)/registered(Latest members)/most_liked(Most liked)/most_followers(Most followers).' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'order'        => array(
                'description' => __( 'asc/desc' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'peepso_gender'        => array(
                'description' => __( 'm/f' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'peepso_avatar'        => array(
                'description' => __( 'flag: 1(Only users with avatars)' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'peepso_following'        => array(
                'description' => __( 'values: -1(All members),0(Members I don’t follow),1(Members I follow)' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'query'        => array(
                'description' => __( 'search string' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
        ];
    }


}

new App_Expert_Peepso_Core_Members_Routes();

