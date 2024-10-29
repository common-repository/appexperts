<?php
//ae_settings_data
class App_Expert_WC_Settings_Product{
    public function __construct() {
        add_filter( "ae_settings_data", array($this,'add_additional_params_to_settings'), 10, 1 );
        add_filter( "ae_post_type_translation_domain", array($this,'add_post_type_translation_domain'), 10, 2 );
    }
    public function add_additional_params_to_settings($data){
        if(class_exists('WC_REST_System_Status_V2_Controller')){
            $System_Status = new WC_REST_System_Status_V2_Controller();
            if(!isset($data ['wc']))$data ['wc']=[];
            $data ['wc']=array_merge($data ['wc'],$System_Status->get_settings());
        }
        if(class_exists('WC_REST_Setting_Options_Controller')){
            $Setting_Options = new WC_REST_Setting_Options_Controller();
            if(!isset($data ['wc']))$data ['wc']=[];
            $data ['wc']['products']=$Setting_Options->get_group_settings('products');
            $data ['wc']['account']=$Setting_Options->get_group_settings('account');
        }
        $data ['wc']['version'] = wc()->version;
       

        $checkout_id=get_option( 'woocommerce_checkout_page_id' );
        $languages =App_Expert_Language::get_active_languages();
        if ( !empty( $languages ) && count($languages)>1) {
            foreach( $languages as $l ) {
                $id=App_Expert_Language::get_element_translation($checkout_id, 'page', true, $l['language_code']);
                $data['wc']['translated_page'][$l['language_code']]=get_permalink($id);
            }
        }

        return $data;
    }
    public function add_post_type_translation_domain($domain, $post_type){
        if($post_type == "product")  $domain = 'woocommerce';
        return  $domain ;
    }
}
new App_Expert_WC_Settings_Product();