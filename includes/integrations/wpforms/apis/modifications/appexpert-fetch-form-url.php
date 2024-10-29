<?php 
class App_Expert_WPForms_Fetch_Form_URL{
    public function __construct(){
        add_filter( "ae_settings_data", array($this,'add_additional_params_to_settings'), 10, 1 );

    }
    public function add_additional_params_to_settings($data){
        $lang_code=get_bloginfo("language");
        $lang_code=explode('-',$lang_code);
        $code= $lang_code[0];
        $translated_pages=[];
        $translated_pages[$code]=get_permalink(get_page_by_path( MOBILE_WPFORMS_PAGE_SLUG ));
        $translated_page=apply_filters('ae_wpml_translated_pages',$translated_pages,MOBILE_WPFORMS_PAGE_SLUG);
       
        $data['wpforms']['translated_page']=$translated_page;
         return $data;
    }

}new App_Expert_WPForms_Fetch_Form_URL();