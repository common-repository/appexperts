<?php

Class App_Expert_Woocommerce_Init extends App_Expert_Integration{

    protected function _include_hooks(){
        parent::_include_hooks();
        // Add custom checkout page
        if (!get_page_by_path(MOBILE_CHECKOUT_PAGE_SLUG)) {

            $post_details = array(
                'post_title'    => 'Mobile App Checkout Page',
                'post_content'  => '[woocommerce_checkout]',
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_type' => 'page',
                'post_slug' =>MOBILE_CHECKOUT_PAGE_SLUG
            );
            wp_insert_post($post_details);
        }

        // Add custom thank you page
        if (!get_page_by_path(MOBILE_THANK_YOU_PAGE_SLUG)) {

            $post_details = array(
                'post_title'    => 'Mobile App Woocommerce Thank You',
                'post_content'  => 'You are not authorized to access this page directly',
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_type' => 'page',
                'post_slug' => MOBILE_THANK_YOU_PAGE_SLUG
            );
            wp_insert_post($post_details);
        }

        //init classes
        new App_Expert_Checkout_Authentication($this);
        new App_Expert_Custom_Checkout($this);
        //backend
        new App_Expert_WC_Notification_Settings($this);
        new App_Expert_WC_send_Notification($this);

    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/woocommerce/";
    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/woocommerce/";
    }
    public function get_dependencies()
    {
        return [
            'woocommerce/woocommerce.php'
        ];
    }
    public function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }

    public function add_logs($arr)
    {
        $arr[] ='woocommerce';
        return $arr;
    }
    public function should_include_files(){
        if(class_exists('woocommerce')){
            return true;
        } 
        return false;
    }
}

new App_Expert_Woocommerce_Init();