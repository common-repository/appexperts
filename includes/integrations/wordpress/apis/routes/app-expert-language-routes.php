<?php

class App_Expert_Language_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes(){
        App_Expert_Route::get(APP_EXPERT_API_NAMESPACE, '/languages/active'                 , "App_Expert_Language_Endpoint@get_active_languages"      ,[]);
    }


}

new App_Expert_Language_Routes();

