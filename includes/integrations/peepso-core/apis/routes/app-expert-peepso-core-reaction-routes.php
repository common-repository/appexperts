<?php

class App_Expert_Peepso_Core_Reactions_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/posts/(?P<act_id>[\d]+)/reaction/(?P<react_id>[\d]+)', "App_Expert_Peepso_Core_Reactions_Endpoint@get_users_reactions", $this->get_single_post_reacted_users(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/add_reaction', "App_Expert_Peepso_Core_Reactions_Endpoint@add_reaction" , $this->add_post_reaction_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/delete_reaction', "App_Expert_Peepso_Core_Reactions_Endpoint@delete_reaction" , $this->delete_post_reaction_parameters(), "App_Expert_Auth_Request");
    }

    public function add_post_reaction_parameters(){
        return  array(
            'act_id'        => array(
                'description' => __( 'Activity id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'post_id'        => array(
                'description' => __( 'Post id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'react_id'        => array(
                'description' => __( 'React id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )

        );

    }

    public function delete_post_reaction_parameters(){
        return  array(
            'act_id'        => array(
                'description' => __( 'Activity id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'post_id'        => array(
                'description' => __( 'Post id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )

        );
    }

    public function get_single_post_reacted_users(){
        return  array(
            'act_id'        => array(
                'description' => __( 'Activity id.' , 'app-expert' ),
                'type'        => 'integer',
            ),
            'react_id'        => array(
                'description' => __( 'reaction id from settings.' , 'app-expert' ),
                'type'        => 'integer',
            ),
            'limit'        => array(
                'description' => __( 'number of users per page.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'page'        => array(
                'description' => __( 'page number.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            )

        );

    }

}

new App_Expert_Peepso_Core_Reactions_Routes();

