<?php

Class App_Expert_Settings_Modification{
    public function __construct(){
        add_filter('ae_save_settings_params'   , array($this, 'add_server_param'), 10, 1);
        add_filter('ae_save_settings_response' , array($this, 'add_server_key')  , 10, 2);
    }
    public function add_server_param($params){
        $params['server_key'] =  array(
            'description'    => __( 'Server Key' , 'app-expert' ),
            'type'           => 'string',
            'context'        => array( 'edit' ),
            'required'       => false
        );
        return $params;
    }

    public function add_server_key($response,$request){
        $serverKey  = $request->get_param( 'server_key' );
        if(!empty($serverKey)){
            update_option( 'server_key', $serverKey);
            $response['code'] = 'server_key_is_updated';
            $response['message'] = 'Server key is updated successfully';
        }else{
            delete_option( 'server_key' );
            $response['code'] = 'server_key_is_removed';
            $response['message'] = 'Server key is removed successfully';
        }
        $response['data']['server_key'] = $serverKey;
        return $response;
    }
}
new App_Expert_Settings_Modification();