<?php
Class App_Experts_SitePress_Multilingual_Cms_Init extends App_Expert_Integration {
    public function get_dir(){
        return plugin_dir_path(__FILE__);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/sitepress-multilingual-cms/";

    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/sitepress-multilingual-cms/";
    }

    public function get_dependencies(){
        return ['sitepress-multilingual-cms/sitepress.php'];
    }

    public function _include_hooks()
    {
        parent::_include_hooks();
        //backend
        new App_Expert_WPML_Language($this);
    }

    public function add_logs($arr)
    {
        $arr[] ='sitepress-multilingual-cms';
        return $arr;
    }
    public function should_include_files(){
        if(class_exists('SitePress')){
            return true;
        } 
        return false;
    }
}
//todo:find a better way to create new object
new App_Experts_SitePress_Multilingual_Cms_Init();