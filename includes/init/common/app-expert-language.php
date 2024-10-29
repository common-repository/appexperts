<?php
class App_Expert_Language
{
    public static function get_active_languages() {
        $langs = array();
        $locale = get_locale();
        require_once( ABSPATH . 'wp-admin/includes/translation-install.php' );
        $translations = wp_get_available_translations();
        if($locale === 'en_US'){
            $langs['en'] = array(
                'code'=> 'en',
                'id'=> '',
                'native_name'=> 'English',
                'major'=> '',
                'active'=> '',
                'default_locale'=> 'en_US',
                'encode_url'=> '',
                'tag'=> '',
                'translated_name'=> 'English',
                'url'=> '',
                'country_flag_url'=> '',
                'language_code'=> ''
            );
        }
        elseif(isset($translations[$locale])){
            $translation = $translations[$locale];
            $langs[current( $translation['iso'] )] = array(
                'code'=> current( $translation['iso'] ),
                'id'=> '',
                'native_name'=> $translation['native_name'],
                'major'=> '',
                'active'=> '',
                'default_locale'=> $translation['language'],
                'encode_url'=> '',
                'tag'=> '',
                'translated_name'=> $translation['english_name'],
                'url'=> '',
                'country_flag_url'=> '',
                'language_code'=> ''
            );
        }
        else{
            $langs['en'] = array(
                'code'=> 'en',
                'id'=> '',
                'native_name'=> 'English',
                'major'=> '',
                'active'=> '',
                'default_locale'=> 'en_US',
                'encode_url'=> '',
                'tag'=> '',
                'translated_name'=> 'English',
                'url'=> '',
                'country_flag_url'=> '',
                'language_code'=> ''
            );
        }
        return apply_filters('ae_get_active_languages',$langs);
    }
    public static function switch_lang($lang){
       do_action('ae_switch_lang',$lang);
    }
    public static function get_user_locale($user_id){
        $local=get_user_meta($user_id,'_as_current_lang',true);
        if(!$local){
            $local = get_user_locale($user_id);
            $local = explode("_",$local);
            $local = $local[0];
        }
        return  apply_filters('ae_get_user_locale',$local,$user_id);
    }
    public static function get_element_translation($element_id, $element_type = 'post', $return_original_if_missing = false, $language_code = null){
        return apply_filters('ae_get_element_translation',$element_id, $element_type,$return_original_if_missing,$language_code);
    }

}
