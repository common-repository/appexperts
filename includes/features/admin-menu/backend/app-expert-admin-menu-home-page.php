<?php
class App_Expert_Admin_Menu_Home_Page{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_action('admin_menu', [$this, 'add_home_page']);
        add_action('admin_enqueue_scripts',  [$this, 'register_scripts']  );
    }
    public function add_home_page()
    {
        add_submenu_page('app_expert',  __( 'Home','app-expert' ), __("Home",'app-expert' ),'manage_options','app_expert', [$this, 'render_home_page'],1);
    }
    public function register_scripts(){
        wp_register_style( 'app_expert_bootstrap_css', $this->_current_feature->get_current_url(). 'assets/css/bootstrap.min.css', false, APP_EXPERT_PLUGIN_VERSION );
        wp_register_style( 'app_expert_menu_pages_css', $this->_current_feature->get_current_url() . 'assets/css/menu-pages.css', ['app_expert_bootstrap_css'], APP_EXPERT_PLUGIN_VERSION );

        wp_register_script('app_expert_menu_pages_js', $this->_current_feature->get_current_url() . 'assets/js/menu-pages.js', ['jquery'], APP_EXPERT_PLUGIN_VERSION,true);
    }
    public function render_home_page(){
        wp_enqueue_style( 'app_expert_bootstrap_css' );
        wp_enqueue_style( 'app_expert_menu_pages_css' );

        wp_enqueue_script( 'app_expert_menu_pages_js' );
        include_once $this->_current_feature->get_current_path()."templates/home.php";
    }
}
