<?php 
class App_Expert_WPForms_Routes
{

    public function __construct(){
        $this->register_routes();
        
    }
    public function register_routes(){
        App_Expert_Route::get(WPFORMS_API_NAMESPACE, 'forms', "App_Expert_WPForms_Endpoint@get_all_forms" , $this->get_forms_parameters());

    }
    public function get_forms_parameters(){
        $parameters = array();
    $parameters = apply_filters('ae_wpforms_forms_parameters', $parameters);
    return $parameters;
    }
}
new App_Expert_WPForms_Routes();