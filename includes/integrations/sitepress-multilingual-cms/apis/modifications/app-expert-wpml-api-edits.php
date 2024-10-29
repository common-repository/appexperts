<?php

class App_Expert_WPML_Apis_edits{

    public function __construct(){
        add_filter('ae_post_name_translation',array($this,'add_wpml_settings'),10,4);
        add_filter('ae_login_user_data_object',array($this,'save_wpml_language'),10,3);
        add_filter('ae_page_webview_permalink',array($this,'get_webview_permalink'),10);
    }
    public function add_wpml_settings($post_name_translation,$post_type,$translation_domain,$locale){
        $val = apply_filters('wpml_translate_single_string', $post_type->data['name'], $translation_domain, $post_type->data['name'], $locale);

        if(!empty($val)) $post_name_translation =$val;
        return $post_name_translation;
    }
    public function save_wpml_language($user_data,$request,$user){
         if(!empty($_REQUEST['wpml_language'])){
            update_user_meta($user->ID,'_as_current_lang',$_REQUEST['wpml_language']);
        }
        return $user_data;
    }
    public function get_webview_permalink($permalink){
        $lang = (isset($_GET['display_language']) && $_GET['display_language']) ? sanitize_key($_GET['display_language']) : null;
        $new_permalink = apply_filters('wpml_permalink', $permalink,  $lang);
        return $new_permalink?:$permalink;
    }
}
new App_Expert_WPML_Apis_edits();