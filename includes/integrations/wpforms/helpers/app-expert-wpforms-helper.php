<?php
class App_Expert_WPForms_Helper {


    public static function is_ae_custom_page($custom_page_slug)
    {
        $saved_page=get_page_by_path($custom_page_slug);
        if(!($saved_page instanceof WP_Post)) return false;
        $request_uri = $_SERVER['REQUEST_URI'];
        $relative_page_permalink = str_replace(home_url(), '', get_permalink($saved_page->ID));
        if (false === strpos($request_uri, $relative_page_permalink)) {
            return false;
        }
        global $post;
        if(!($post instanceof WP_Post)) {
            $post = $saved_page;
        }
     $languages =App_Expert_Language::get_active_languages();
     $flag=false;
     if ( !empty( $languages ) && count($languages)>1) {
         foreach( $languages as $l ) {
             $flag=($post->ID==App_Expert_Language::get_element_translation($saved_page->ID, 'page', true, $l['language_code']));
             if($flag) break;
         }
     }else{
         $flag=true;
     }
     return $flag;
    }
   
}