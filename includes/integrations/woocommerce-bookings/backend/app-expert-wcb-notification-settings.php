<?php
class App_Expert_WCB_Notification_Settings{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        add_action('admin_init', [$this, 'register_settings'],20);

    }
    public function register_settings()
    {
        add_settings_field('booking_notification_settings', __('Booking', 'app-expert'), [$this, 'add_booking_notification_settings'], 'notification-options', 'App Experts');
    }
    public function add_booking_notification_settings(){
        $options = get_option('notification_options');
        $woocommerce_options = [
            [
                'title' => __('Booking Reminder', 'app-expert'),
                'value' => 'booking_reminder'
            ],[
                'title' => __('Booking Confirmed', 'app-expert'),
                'value' => 'booking_confirmed'
            ],[
                'title' => __('Booking Send Notification', 'app-expert'),
                'value' => 'booking_manual_notification'
            ],[
                'title' => __('Admin Cancel Booking', 'app-expert'),
                'value' => 'admin_cancel_booking'
            ],
            [
                'title' => __('Complete and Rate Booking', 'app-expert'),
                'value' => 'complete_and_rate_booking'
            ]
        ];
        //todo : do this in a template & call it here
        foreach ($woocommerce_options as $option ):?>
            <input type="checkbox" name="notification_options[booking_settings][<?php echo $option['value'] ?>]" value="<?php echo $option['value'] ?>" <?php checked(isset($options['booking_settings'][$option['value']])); ?> /> <?php echo $option['title'] ;?><br />
        <?php endforeach;
    }
}
