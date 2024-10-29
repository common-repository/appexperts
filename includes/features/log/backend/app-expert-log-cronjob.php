<?php
class App_Expert_Log_CronJob{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_action('ae_daily_log',array($this,'add_logs'));

        $this->add_schedule();

    }

    private function add_schedule()
    {
        if (! wp_next_scheduled ( 'ae_daily_log' )) {
            $today=date('Y-m-d 00:01:00');
            wp_schedule_event(strtotime($today), 'daily', 'ae_daily_log');
        }
    }
    public function add_logs(){
        global $wp_version;
        include_once( 'wp-admin/includes/plugin.php' );
        $all_plugins = get_plugins();
        $active_plugins  = apply_filters('active_plugins', get_option('active_plugins'));
        $active_plugins_data=[];
        foreach ($active_plugins as $active_plugin){
            $active_plugins_data[]= $all_plugins[$active_plugin]??"Not active";
        }

        App_Expert_Logger::info('App Expert Version            : '.APP_EXPERT_PLUGIN_VERSION);
        App_Expert_Logger::info('Wordpress Version             : '.$wp_version);
        App_Expert_Logger::info('Php Version                   : '.phpversion());
        App_Expert_Logger::info('Site URL                      : '.get_site_url());
        App_Expert_Logger::info('Rest Route URL                : '.get_rest_url());
        App_Expert_Logger::info('App Expert Custom Settings    : ',apply_filters('ae_daily_log_custom_settings',[]));
        App_Expert_Logger::info('Current Active Integrations   : ',apply_filters('ae_daily_log_custom_integration',[]));
        App_Expert_Logger::info('Current Active Theme          : ',(array)wp_get_theme());
        App_Expert_Logger::info('Current Default Language      : '.get_locale());
        App_Expert_Logger::info('Current Active Languages      : ',App_Expert_Language::get_active_languages());
        App_Expert_Logger::info('Current Active Plugins        : ',$active_plugins_data);
        App_Expert_Logger::info('========================================================================================================');


    }


}
