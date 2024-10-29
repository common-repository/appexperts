<?php
class App_Expert_app_Custom_Checkout_Helper {

    public static function check_page($slug){
        $saved_page=get_page_by_path($slug);
        if(!($saved_page instanceof WP_Post)) return false;
        $url = explode("?",$_SERVER['REQUEST_URI']);
        $request_uri = str_replace("/","",$url[0]);
        $relative_page_permalink = str_replace(home_url(), '', get_permalink($saved_page->ID));
        if (false === strpos( urldecode($relative_page_permalink), urldecode($request_uri))) {
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
    public static function is_checkout_custom_page()
    {
         if(isset($_GET['ae_mobile_view'])){
             $checkout_id = get_option( 'woocommerce_checkout_page_id' );
             $flag        = self::check_page(get_post_field( 'post_name',$checkout_id));
            if($flag) return $flag;
         }
        return self::check_page(MOBILE_CHECKOUT_PAGE_SLUG);
    }

    public static function is_thank_you_custom_page()
    {
        return is_page(MOBILE_THANK_YOU_PAGE_SLUG);
    }
}