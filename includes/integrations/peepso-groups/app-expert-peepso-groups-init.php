<?php

Class App_Expert_Peepso_Groups_Init extends App_Expert_Integration{

    protected function _include_hooks(){
        parent::_include_hooks();
        //backend
        new App_Expert_Peepso_Groups_Notification_Settings($this);
        new App_Expert_Peepso_Groups_Send_Notification($this);
        //frontend
        new App_Expert_Peepso_Group_Profile_Authentication($this);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/peepso-groups/";
    }

    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/peepso-groups/";
    }

    public function get_dependencies()
    {
        return [
            'peepso-core/peepso.php',
            'peepso-groups/groups.php'
        ];
    }

    function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }
    public function is_dependencies_allowed(){
        $PLUGIN_EDD = 67133;
        $PLUGIN_SLUG = 'groupso';
        if (!PeepSoLicense::check_license($PLUGIN_EDD, $PLUGIN_SLUG)) {
            return false;
        }
        return true;
    }

    public function add_logs($arr)
    {
        $arr[] ='peepso-groups';
        return $arr;
    }
    public function is_classes_dependencies(){
        return class_exists('PeepSoGroupsPlugin') && class_exists('PeepSoLicense');
    }
    public function should_include_files(){
        if(class_exists('PeepSo')&&class_exists('PeepSoGroupsPlugin') &&class_exists('PeepSoLicense')&&$this->is_dependencies_allowed()){
            return true;
        } 
        return false;
    }
}

new App_Expert_Peepso_Groups_Init();