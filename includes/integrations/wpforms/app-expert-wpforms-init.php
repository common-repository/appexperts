<?php
Class App_Expert_WPForms_Init extends App_Expert_Integration{
   
    public function get_dir(){
        return plugin_dir_path(__FILE__);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/wpforms/";
    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/wpforms/";
    }
    public function get_dependencies(){
        return ['wpforms-lite/wpforms.php'];
    }
    public function _include_hooks(){
        parent::_include_hooks();     
        new App_Expert_Render_Form();
        new App_Expert_WPForms_Page($this);

    }

    public function add_logs($arr){
        $arr[] ='wpforms';
        return $arr;
    }
    public function should_include_files(){
        if(class_exists('wpforms')){
            return true;
        } 
        return false;
    }
  
}
new App_Expert_WPForms_Init();