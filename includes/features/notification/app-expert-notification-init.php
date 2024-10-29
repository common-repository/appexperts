<?php

Class App_Expert_Notification_Init extends App_Expert_Feature{
    public function _include_hooks(){
        parent::_include_hooks();
        $this->_inst_classes();
    }

    public function _inst_classes(){
        new App_Expert_Admin_Menu_Manual_Push_Notification_Page($this);
        new App_Expert_Admin_Menu_Notifications_Page($this); 
        new App_Expert_Notification_Settings($this);    
    }
    public function get_current_url(){
        return  APP_EXPERT_URL."includes/features/notification/";

    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/features/notification/";
    }
    function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }
}
//todo:find a better way to create new object
new App_Expert_Notification_Init();