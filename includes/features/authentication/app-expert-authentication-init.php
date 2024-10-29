<?php

Class App_Expert_Authentication_Init extends App_Expert_Feature{

    function _include_hooks()
    {
        parent::_include_hooks();
        new App_Expert_Auth_Middleware();
        register_activation_hook(APP_EXPERT_FILE, array($this, 'add_jwt_secret_key'));
    }
    public function _init_endpoints(){
        parent::_init_endpoints();
    }
    public function add_jwt_secret_key()
    {
        $settings = get_option('app_expert_settings',[]);

        if (!isset($settings['secret_key']) || empty($settings['secret_key'])) {

            $settings['secret_key'] = wp_hash(get_site_url() . mt_rand(0, 10000));
            update_option('app_expert_settings', $settings);
        }
    }
    function get_dir()
    {
       return plugin_dir_path(__FILE__);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/features/authentication/";

    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/features/authentication/";
    }
}
new App_Expert_Authentication_Init();