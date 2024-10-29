<?php

Class App_Expert_Yith_Woocommerce_Wishlist_Init extends App_Expert_Integration{

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/yith-woocommerce-wishlist/";
    }

    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/yith-woocommerce-wishlist/";
    }

    public function get_dependencies()
    {
        return [
            'yith-woocommerce-wishlist/init.php'
        ];
    }

    function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }

    public function add_logs($arr)
    {
        $arr[] ='yith-woocommerce-wishlist';
        return $arr;
    }
    public function should_include_files(){
        if(class_exists('YITH_WCWL')){
            return true;
        } 
        return false;
    }
}

new App_Expert_Yith_Woocommerce_Wishlist_Init();