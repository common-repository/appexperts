<?php
class App_Expert_Peepso_Friends_Notification_Settings{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        add_action('admin_init', [$this, 'register_settings'],60);

    }

    public function register_settings()
    {
        add_settings_field('peepso_friends_notification_settings', __('Peepso Friends', 'app-expert'), [$this, 'add_notification_settings'], 'notification-options', 'App Experts');
    }


    public function add_notification_settings()
    {
        $options = get_option('notification_options');
        $peepso_friends_options = [
            [
                'title' => __('Receive request', 'app-expert'),
                'value' => 'receive_request'
            ],
            [
                'title' => __('Accept request', 'app-expert'),
                'value' => 'accept_request'
            ]
        ];
        //todo : do this in a template & call it here
        foreach ($peepso_friends_options as $option ): ?>
          <input type="checkbox"
                 name="notification_options[peepso_friends_settings][<?php echo $option['value']  ?>]"
                 value="<?php echo $option['value'] ?>"
                <?php checked(isset($options['peepso_friends_settings'][$option['value']])); ?> />
            <?php echo $option['title'] ;?>
            <br />
        <?php
endforeach;
    }
}
