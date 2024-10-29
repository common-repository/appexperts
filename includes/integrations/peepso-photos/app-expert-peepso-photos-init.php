<?php

Class App_Expert_Peepso_Photos_Init extends App_Expert_Integration{

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/peepso-photos/";
    }

    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/peepso-photos/";
    }

    public function get_dependencies()
    {
        return [
            'peepso-core/peepso.php',
            'peepso-photos/peepsosharephotos.php'
        ];
    }

    function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }
    public function is_dependencies_allowed(){
        $PLUGIN_EDD = 221;
        $PLUGIN_SLUG = 'picso';
        if (!PeepSoLicense::check_license($PLUGIN_EDD, $PLUGIN_SLUG)) {
            return false;
        }
        return true;
    }

    public function add_logs($arr)
    {
        $arr[] ='peepso-photos';
        return $arr;
    }
    public function is_classes_dependencies(){
        return class_exists('PeepSoSharePhotos') && class_exists('PeepSoLicense');
    }
    public function should_include_files(){
        if(class_exists('PeepSo')&&class_exists('PeepSoSharePhotos') &&class_exists('PeepSoLicense')&&$this->is_dependencies_allowed()){
            return true;
        } 
        return false;
    }
}

new App_Expert_Peepso_Photos_Init();