<?php

Class App_Expert_Search_Management_Init extends App_Expert_Feature{

    function _include_hooks()
    {
        parent::_include_hooks();
    }
    
    public function _init_endpoints(){
        parent::_init_endpoints();
    }

    function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/features/search-management/";

    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/features/search-management/";
    }
}
new App_Expert_Search_Management_Init();