<?php
//ae_settings_data
class App_Expert_Peepso_Photos_Settings{
    public function __construct() {
        add_filter( "ae_settings_data", array($this,'add_additional_params_to_settings'), 10, 1 );
    }
    public function add_additional_params_to_settings($data){

        $settings = array(
            'photos_max_upload_size' => '20',
            'photos_max_image_width' => '4000',
            'photos_max_image_height' => '3000',
            'photos_quality_full' => '85',
        );

        foreach($settings as $key=>$default){
            $data["peepso"][$key] = (string)PeepSo::get_option( $key , $default );
        }

        return $data;
    }

}
new App_Expert_Peepso_Photos_Settings();