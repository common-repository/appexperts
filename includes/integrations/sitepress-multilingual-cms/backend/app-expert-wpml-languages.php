<?php

//todo: separate each action in a class
class App_Expert_WPML_Language{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        add_filter('ae_get_active_languages',array($this,'add_wpml_languages'));
        add_action('ae_switch_lang',array($this,'switch_wpml_lang'));
        add_filter('ae_get_element_translation',array($this,'get_element_translation'),1,4);
    }
    public function add_wpml_languages($langs){
        $_langs = apply_filters('wpml_active_languages', NULL, 'orderby=id&order=asc');
        if($_langs){
            $langs = $_langs;
        }
        return $langs;
    }
    public function switch_wpml_lang($lang){
        global $sitepress;
        $sitepress->switch_lang($lang,true);
        do_action( 'wpml_switch_language', $lang );
    }

    public function get_element_translation($element_id, $element_type,$return_original_if_missing,$language_code){
        return apply_filters('wpml_object_id',
            $element_id,
            $element_type,
            $return_original_if_missing,
            $language_code);
    }
}
