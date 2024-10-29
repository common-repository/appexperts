<?php
Class App_Expert_Freemius_Init extends App_Expert_Integration{
    public function get_dir(){
        return plugin_dir_path(__FILE__);
    }

    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/freemius/";
    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/freemius/";
    }
    public function get_dependencies(){
        return [];
    }
    public function add_logs($arr){
        $arr[] ='freemius';
        return $arr;
    }

    protected function _include_hooks(){
        parent::_include_hooks();
        $this->register_freemius();
    }

    public function app_expert_fs(){
        global $App_Expert_fs;
        if (!isset($App_Expert_fs)) {
            // Include Freemius SDK.
            require_once APP_EXPERT_PATH . '/freemius/start.php';
            $App_Expert_fs = fs_dynamic_init(array(
                'id' => '8460',
                'slug' => 'appexperts',
                'type' => 'plugin',
                'public_key' => 'pk_45a4ed26beae8ad2f14c9618984f9',
                'is_premium' => false,
                'has_addons' => false,
                'has_paid_plans' => false,
                'menu' => array(
                    'slug' => 'app_experts',
                    'override_exact' => true,
                    'account' => false,
                    'contact' => false,
                    'support' => false,
                    'parent' => array(
                        'slug' => 'admin.php?page=app_expert',
                    ),
                ),
                // Set the SDK to work in a sandbox mode (for development & testing).
                // IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
                'secret_key' => 'sk_sxbpc(F9V5!SU^^nER:Zmufa5N0S8',
            ));
        }
        return $App_Expert_fs;
    }

    public function app_experts_fs_settings_url()
    {
        return admin_url('admin.php?page=app_expert');
    }

    public function register_freemius(){
        // Init Freemius.
        $freemius=$this->app_expert_fs();
        // Signal that SDK was initiated.
        do_action('App_Expert_fs_loaded');

        $freemius->add_filter('connect_url', [$this,'app_experts_fs_settings_url']);
        $freemius->add_filter('after_skip_url', [$this,'app_experts_fs_settings_url']);
        $freemius->add_filter('after_connect_url', [$this,'app_experts_fs_settings_url']);
        $freemius->add_filter('after_pending_connect_url', [$this,'app_experts_fs_settings_url']);

    }
    public function should_include_files(){ 
        return true;
    }
}
new App_Expert_Freemius_Init();