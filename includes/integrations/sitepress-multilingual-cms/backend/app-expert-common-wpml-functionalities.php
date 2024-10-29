<?php 
class App_Expert_Common_WPML_Functionalities{
public function __construct(){
    add_filter('ae_wpml_translated_pages',array($this,'get_translated_pages_url'),10,2);
}
    public  function get_translated_pages_url($translated_pages,$slug){
        if ( defined( 'ICL_SITEPRESS_VERSION' ) && class_exists( 'SitePress' ) ) {
            global $wpml_url_converter;
                $site_url_default_lang = $wpml_url_converter->get_default_site_url();
             	$active_languages = apply_filters( 'wpml_active_languages', null, null );
                $translated_pages=array();
                if($active_languages){
                    foreach($active_languages as $key=>$value){
                        $translated_pages[$value['code']]=$wpml_url_converter->convert_url( get_permalink( get_page_by_path( $slug ) ), $value['code'] );
                    }
                }else{
                    $lang_code=get_bloginfo("language");
                    $lang_code=explode('-',$lang_code);
                    $code= $lang_code[0];
                    $translated_pages[$code]=get_permalink(get_page_by_path( $slug ));
                }
        }
        // else{
        //     $lang_code=get_bloginfo("language");
        //     $lang_code=explode('-',$lang_code);
        //     $code= $lang_code[0];
        //      $translated_pages[$lang_codecode]=get_permalink(get_page_by_path( $slug ));

        // }
        return  apply_filters('ae_wpforms_translation_mapping',$translated_pages);
    }
}new App_Expert_Common_WPML_Functionalities();
