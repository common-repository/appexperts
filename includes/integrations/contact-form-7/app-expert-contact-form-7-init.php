<?php
Class App_Expert_Contact_Form_7_Init extends App_Expert_Integration{
    public function get_dir(){
        return plugin_dir_path(__FILE__);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/contact-form-7/";
    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/contact-form-7/";
    }
    public function get_dependencies(){
        return ['contact-form-7/wp-contact-form-7.php'];
    }
    public function _include_hooks(){
        parent::_include_hooks();
        new App_Expert_Contact_Form_7_Auth_Middleware();
    }

    public function add_logs($arr){
        $arr[] ='contact-form-7';
        return $arr;
    }
    public function should_include_files(){
        if(class_exists('WPCF7')){
            return true;
        }
        return false;
    }
}
new App_Expert_Contact_Form_7_Init();