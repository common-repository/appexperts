<?php

class App_Expert_Settings_Routes {

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes(){
        //settings
        App_Expert_Route::get(APP_EXPERT_API_NAMESPACE, '/settings'   , "App_Expert_Settings_Endpoint@get_items"        , $this->get_collection_parameters());
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE,'/settings'   , "App_Expert_Settings_Endpoint@save_settings"    , $this->get_save_settings_parameters());

        //license key
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/settings/validate_license_key'   , "App_Expert_Settings_Endpoint@validate_license_key"     , $this->get_license_parameters());

        //app_token
        //Note: this part is separated from setting because it gets called every time the mobile app opens so the call needs to be lite weight
        App_Expert_Route::get(APP_EXPERT_API_NAMESPACE, '/settings/get_app_token'    , "App_Expert_Settings_Endpoint@get_app_token"  ,[]);

    }

    public function get_collection_parameters() {
        return array(
            'context' =>  array(
                'description'       => __( 'Scope under which the request is made; determines fields present in response.' ),
                'type'              => 'string',
                'default'           => 'view'
            ) ,
        );
    }
    public function get_license_parameters(){
        return  array(
            'license_key'        => array(
                'description' => __( 'License Key' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'edit' ),
                'required' => true
            )

        );
    }
    public function get_save_settings_parameters(){
        return apply_filters("ae_save_settings_params",array(
            'license_key'        => array(
                'description'    => __( 'License Key' , 'app-expert' ),
                'type'           => 'string',
                'context'        => array( 'edit' ),
                'required'       => true
            ),
            'app_token'          => array(
                'description'    => __( 'app auth token' , 'app-expert' ),
                'type'           => 'string',
                'context'        => array( 'edit' ),
                'required'       => false
            ),
            'api_key'            => array(
                'description'    => __( 'Api Key' , 'app-expert' ),
                'type'           => 'string',
                'context'        => array( 'edit' ),
                'required'       => false
            )
        ));
    }
}

new App_Expert_Settings_Routes();

