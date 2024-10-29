<?php

Class App_Expert_Peepso_Videos_Init extends App_Expert_Integration{

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/peepso-videos/";
    }

    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/peepso-videos/";
    }

    public function get_dependencies()
    {
        //todo: check dependancies
        return [
            'peepso-core/peepso.php',
            'peepso-videos/peepsovideos.php'
        ];
    }

    function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }
    public function is_dependencies_allowed(){
        $PLUGIN_EDD = 245;
        $PLUGIN_SLUG = 'vidso';
        if (!PeepSoLicense::check_license($PLUGIN_EDD, $PLUGIN_SLUG)) {
            return false;
        }
        return true;
    }

    public function add_logs($arr)
    {
        $arr[] ='peepso-videos';
        return $arr;
    }
    public function is_classes_dependencies(){
        return class_exists('PeepSoVideos') && class_exists('PeepSoLicense');
    }
    public function should_include_files(){
        if(class_exists('PeepSo')&&class_exists('PeepSoVideos') &&class_exists('PeepSoLicense')&&$this->is_dependencies_allowed()){
            return true;
        } 
        return false;
    }
}

new App_Expert_Peepso_Videos_Init();