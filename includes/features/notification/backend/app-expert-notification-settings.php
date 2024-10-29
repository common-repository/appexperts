<?php
class App_Expert_Notification_Settings{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_filter('ae_settings_tabs',array($this, 'add_notification_tab'), 10, 1);
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('ae_daily_log_custom_settings',array($this,"add_logs"));
    }

    public function register_settings()
    {
        register_setting('notification_options', 'notification_options');
        add_settings_section('App Experts', __('Manage automatic notification triggers', 'app-expert'), [$this, 'get_section_text'], 'notification-options');
        add_settings_field('general_notification_settings', __('General', 'app-expert'), [$this, 'get_section_inputs'], 'notification-options', 'App Experts');
    }

    public function get_section_inputs()
    {
        $options = get_option('notification_options');
        $general_options = [
            [
                'title' => __('User Signup', 'app-expert'),
                'value' => 'user_signup'
            ],[
                'title' =>  __('New blog post added', 'app-expert'),
                'value' => 'new_blog_post_added'
            ]
        ];
        foreach ($general_options as $option ):?>
            <input type="checkbox" name="notification_options[general_settings][<?php echo $option['value'] ?>]" value="<?php echo $option['value'] ?>" <?php checked(isset($options['general_settings'][$option['value']])); ?> /> <?php echo $option['title'] ;?><br />
        <?php endforeach;
    }

    public function get_section_text()
    {
        echo '<p>'.__('Please select the options that you want them to use when your website push notification.', 'app-expert').'</p>';
    }

    public function add_notification_tab ($tabs)
    {
        $tabs['notification'] = [
            "tab_name"=>  __( 'notification', 'app-expert' ),
            "tab_view"=>  $this->_current_feature->get_current_path() . "templates/settings-tabs/notification.php"
        ];
        return $tabs;
    }
    public function add_logs($settings_array){
        $server_key = get_option('server_key');
        $settings_array['notification']=   [
            "is_allowed"=>!empty($server_key)?"yes":"no",
            "automatic_send_allowed"=>get_option('notification_options')
        ];

        return $settings_array;
    }

}
