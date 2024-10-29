<?php
class App_Expert_WC_Notification_Settings{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        add_action('admin_init', [$this, 'register_settings'],20);

    }

    public function register_settings()
    {
           add_settings_field('woocommerce_notification_settings', __('Woocommerce', 'app-expert'), [$this, 'add_notification_settings'], 'notification-options', 'App Experts');
    }


    public function add_notification_settings()
    {
        $options = get_option('notification_options');
        $woocommerce_options = [
            [
                'title' => __('Order status changed', 'app-expert'),
                'value' => 'order_status_changed'
            ],[
                'title' => __('Order note added', 'app-expert'),
                'value' => 'order_note_added'
            ],[
                'title' => __('Product review approved', 'app-expert'),
                'value' => 'product_review_approved'
            ],[
                'title' => __('Product review rejected', 'app-expert'),
                'value' => 'product_review_rejected'
            ]
        ];
        //todo : do this in a template & call it here
        foreach ($woocommerce_options as $option ): ?>
          <input type="checkbox"
                 name="notification_options[woocommerce_settings][<?php echo $option['value']  ?>]"
                 value="<?php echo $option['value'] ?>"
                <?php checked(isset($options['woocommerce_settings'][$option['value']])); ?> />
            <?php echo $option['title'] ;?>
            <br />
        <?php
endforeach;
    }
}
