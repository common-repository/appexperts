<?php

Class App_Expert_Peepso_Core_Init extends App_Expert_Integration{

    protected function _include_hooks(){
        parent::_include_hooks();
        //backend
        new App_Expert_Peepso_Core_Notification_Settings($this);
        new App_Expert_Peepso_Core_send_Notification($this);
        //frontend
        new App_Expert_Peepso_Core_Profile_Authentication($this);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/peepso-core/";
    }

    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/peepso-core/";
    }

    public function get_dependencies()
    {
        return [
            'peepso-core/peepso.php'
        ];
    }

    function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }

    public function add_logs($arr){
        $arr[] ='peepso-core';
        return $arr;
    }
    public function is_classes_dependencies(){
        return class_exists('PeepSoSystemRequirements') || class_exists('PeepSoLicense');
    }
    public function should_include_files(){
        if(class_exists('PeepSo')&&class_exists('PeepSoSystemRequirements') || class_exists('PeepSoLicense')){
            return true;
        } 
        return false;
    }
}

new App_Expert_Peepso_Core_Init();