<?php
Class App_Expert_Admin_Menu_Init extends App_Expert_Feature{
    function get_dir()
    {
        return plugin_dir_path(__FILE__);
    }
    public function _include_hooks(){
        parent::_include_hooks();
        $this->_inst_classes();
    }
    public function _inst_classes(){
        new App_Expert_Admin_Menu($this);
        new App_Expert_Admin_Menu_Home_Page($this);
        new App_Expert_Admin_Menu_Settings($this);
        new App_Expert_Admin_Menu_License($this);
        new App_Expert_Admin_Menu_Mobile_pages($this);
    }
    public function get_current_url(){
        return  APP_EXPERT_URL."includes/features/admin-menu/";

    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/features/admin-menu/";
    }
}
//todo:find a better way to create new object
new App_Expert_Admin_Menu_Init();