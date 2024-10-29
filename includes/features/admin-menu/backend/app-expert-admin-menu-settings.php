<?php
class App_Expert_Admin_Menu_Settings{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_enqueue_scripts',  [$this, 'register_scripts']  );
    }
    public function add_settings_page()
    {
        add_submenu_page('app_expert',  __( 'Settings','app-expert' ), __("Settings",'app-expert' ),'manage_options','app_expert_settings', [$this, 'render_settings_page'],2);
    }
    public function register_scripts(){
        wp_register_style( 'app_expert_settings_pages_css', $this->_current_feature->get_current_url() . 'assets/css/settings-page.css', ['bootstrap_css'], APP_EXPERT_PLUGIN_VERSION );
        wp_register_script('app_expert_copy_to_clipboard', $this->_current_feature->get_current_url() . 'assets/js/copy-to-clipboard.js', array(), APP_EXPERT_PLUGIN_VERSION, true);

    }
    public function render_settings_page()
    {
        wp_enqueue_style('app_expert_settings_pages_css' );
        wp_enqueue_script('app_expert_copy_to_clipboard' );
        include_once $this->_current_feature->get_current_path()."templates/settings.php";
    }
}
