<?php
class App_Expert_Admin_Menu_License{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_action('admin_menu', [$this, 'add_license_page']);
        add_action('admin_enqueue_scripts',  [$this, 'register_scripts']  );
    }
    public function add_license_page()
    {
        add_submenu_page('app_expert',  __( 'License','app-expert' ), __("License",'app-expert' ),'manage_options','app_expert_license', [$this, 'render_license_page'],3);    
    }
    public function register_scripts(){
        wp_register_script('app_expert_copy_to_clipboard', $this->_current_feature->get_current_url() . 'assets/js/copy-to-clipboard.js', array(), APP_EXPERT_PLUGIN_VERSION, true);

    }
    public function render_license_page()
    {
        wp_enqueue_script('app_expert_copy_to_clipboard' );
        include_once $this->_current_feature->get_current_path()."templates/license.php";
    }
}
