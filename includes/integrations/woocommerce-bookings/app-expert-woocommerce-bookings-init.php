<?php

Class App_Expert_Woocommerce_Bookings_Init extends App_Expert_Integration{

    protected function _include_hooks(){
        parent::_include_hooks();
        //backend
        new APP_EXPERT_WCB_Auto_Send_Email($this);
        new App_Expert_WCB_Notification_Settings($this);
        new App_Expert_WCB_send_Notification($this);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/woocommerce-bookings/";
    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/woocommerce-bookings/";
    }
    public function get_dependencies()
    {
        return [
            'woocommerce/woocommerce.php',
            'woocommerce-bookings/woocommerce-bookings.php'
        ];
    }
    public function get_dir(){
       return plugin_dir_path(__FILE__);
    }

    public function add_logs($arr)
    {
        $arr[] ='woocommerce-bookings';
        return $arr;
    }
    public function is_classes_dependencies(){
        return class_exists('WC_Bookings');
    }
    public function should_include_files(){
        if(class_exists('woocommerce')&&class_exists('WC_Bookings')){
            return true;
        } 
        return false;
    }
}

new App_Expert_Woocommerce_Bookings_Init();