<?php
class App_Expert_Admin_Menu_Manual_Push_Notification_Page{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_enqueue_scripts',  [$this, 'register_scripts']  );
    }

    public function add_settings_page()
    {
        $server_key = get_option('server_key');
        if(!empty($server_key)) {
            add_submenu_page('app_expert', __('Manual Push Notification', 'app-expert'), __("Manual Push Notification", 'app-expert'), 'manage_options', 'manual_push_notification', [$this, 'render_manual_push_notification_page'], 3);
        }
    }

    public function register_scripts(){
        //todo: find a better way to include duplicated assets
        wp_register_style( 'app_expert_bootstrap_css', $this->_current_feature->get_current_url() . 'assets/css/bootstrap.min.css', false, APP_EXPERT_PLUGIN_VERSION );
        wp_register_style( 'app_expert_select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all' );
        wp_register_style( 'app_expert_manual_push_css', $this->_current_feature->get_current_url(). 'assets/css/manual-push-page.css', false, APP_EXPERT_PLUGIN_VERSION, 'all' );

        wp_register_script('app_expert_bootstrap_js',$this->_current_feature->get_current_url() . 'assets/js/bootstrap.js', array(), APP_EXPERT_PLUGIN_VERSION, true);
        wp_register_script('app_expert_select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array( 'jquery' ), APP_EXPERT_PLUGIN_VERSION, true );
        wp_register_script('app_expert_upload_script', $this->_current_feature->get_current_url(). 'assets/js/upload_custom_script.js', array( 'jquery' ,'select2'), APP_EXPERT_PLUGIN_VERSION );
    }

    public function render_manual_push_notification_page()
    {
        if ( ! did_action( 'wp_enqueue_media' ) ) {
            wp_enqueue_media();
        }
        wp_enqueue_style( 'app_expert_bootstrap_css' );
        wp_enqueue_style( 'app_expert_select2css' );
        wp_enqueue_style( 'app_expert_manual_push_css' );

        wp_enqueue_script( 'app_expert_select2' );
        wp_enqueue_script( 'app_expert_bootstrap_js' );
        wp_enqueue_script( 'app_expert_upload_script' );
        include_once $this->_current_feature->get_current_path()."templates/manual-push-notification.php";
    }
    
    public function validate_manual_push(){
        $langs = App_Expert_Language::get_active_languages();
        $errors_arr = [];
        if(isset($_POST['save_send_notification'])){
            foreach ($langs as $lang=>$obj){
                if(empty($_POST['title'][$lang])){
                    $errors_arr['title_error'] = true;
                    break;
                }
                if(strlen($_POST['title'][$lang]) > 50 ){
                    $errors_arr['title_length_error'] = true;
                    break;
                }

                if(empty($_POST['content'][$lang])){
                    $errors_arr['content_error'] = true;
                    break;
                }

                if(strlen($_POST['content'][$lang]) > 100 ){
                    $errors_arr['content_length_error'] = true;
                    break;
                }
            }
            if(!empty($_POST['target'])&&filter_var($_POST['target'], FILTER_VALIDATE_URL) === FALSE ){
                $errors_arr['target_error'] = true;
            }
        }
        return $errors_arr;
    }

    public function save_manual_push(){
        $saved=false;
        if(isset($_POST['save_send_notification'])){
            $savedSegments=$_POST['segments']??['loggedin'];
            if(isset($_POST['is_send_to_guest'])){
                $savedSegments[]='guest';
            }
            $_notification = App_Expert_Notification_Helper::send_manual_push(
                $_POST['title'],
                $_POST['content'],
                $_POST['target']??null,
                $savedSegments??[],
                $_POST['image']??null);
            if($_notification){
                $saved=true;
            }
        }
        return $saved;
    }
}
