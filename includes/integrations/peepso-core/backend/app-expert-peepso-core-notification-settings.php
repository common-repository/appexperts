<?php
class App_Expert_Peepso_Core_Notification_Settings{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        add_action('admin_init', [$this, 'register_settings'],40);

    }

    public function register_settings()
    {
           add_settings_field('peepso_core_notification_settings', __('Peepso Core', 'app-expert'), [$this, 'add_notification_settings'], 'notification-options', 'App Experts');
    }


    public function add_notification_settings()
    {
        $options = get_option('notification_options');
        $peepso_core_options = [
            [
                'title' => __('Post added', 'app-expert'),
                'value' => 'post_added'
            ],[
                'title' => __('Post updated', 'app-expert'),
                'value' => 'post_updated'
            ],[
                'title' => __('Comment added', 'app-expert'),
                'value' => 'comment_added'
            ],[
                'title' => __('Like post', 'app-expert'),
                'value' => 'like_post'
            ],[
                'title' => __('Like comment', 'app-expert'),
                'value' => 'like_comment'
            ],[
                'title' => __('Like Profile', 'app-expert'),
                'value' => 'like_profile'
            ],[
                'title' => __('Mention in post', 'app-expert'),
                'value' => 'mention_in_post'
            ],[
                'title' => __('Mention in comment', 'app-expert'),
                'value' => 'mention_in_comment'
            ]
        ];
        //todo : do this in a template & call it here
        foreach ($peepso_core_options as $option ): ?>
          <input type="checkbox"
                 name="notification_options[peepso_core_settings][<?php echo $option['value']  ?>]"
                 value="<?php echo $option['value'] ?>"
                <?php checked(isset($options['peepso_core_settings'][$option['value']])); ?> />
            <?php echo $option['title'] ;?>
            <br />
        <?php
endforeach;
    }
}
