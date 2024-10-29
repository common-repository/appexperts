<?php

class App_Expert_Peepso_Core_Taggable_Friends_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/taggable-friends', "App_Expert_Peepso_Core_Taggable_Friends_Endpoint@get" , $this->get_taggables_list_parameters(), "App_Expert_Auth_Request");
    }

    public function get_taggables_list_parameters(){
        return [
            'act_id'        => array(
                'description' => __( 'activity id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'search_letter'        => array(
                'description' => __( 'search letter' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        ];
    }
}

new App_Expert_Peepso_Core_Taggable_Friends_Routes();

