<?php
class App_Expert_Peepso_Groups_Notification_Settings{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        add_action('admin_init', [$this, 'register_settings'],50);
    }

    public function register_settings()
    {
           add_settings_field('peepso_groups_notification_settings', __('Peepso Groups', 'app-expert'), [$this, 'add_notification_settings'], 'notification-options', 'App Experts');
    }

    public function add_notification_settings()
    {
        $options = get_option('notification_options');
        $peepso_groups_options = [
            [
                'title' => __('New member added', 'app-expert'),
                'value' => 'new_member_added'
            ],[
                'title' => __('Member post in group', 'app-expert'),
                'value' => 'member_post_in_group'
            ],[
                'title' => __('Invite user', 'app-expert'),
                'value' => 'invite_member'
            ],[
                'title' => __('User accept group invitation', 'app-expert'),
                'value' => 'user_accept_group_invitation'
            ],[
                'title' => __('New join request', 'app-expert'),
                'value' => 'new_join_request'
            ],[
                'title' => __('User\'s request has been accepted', 'app-expert'),
                'value' => 'user_request_has_been_accepted'
            ],[
                'title' => __('Rename Group', 'app-expert'),
                'value' => 'rename_group'
            ],[
                'title' => __('Change group privacy', 'app-expert'),
                'value' => 'change_group_privacy'
            ],[
                'title' => __('Publish Group', 'app-expert'),
                'value' => 'publish_group'
            ],[
                'title' => __('Unpublish Group', 'app-expert'),
                'value' => 'unpublish_group'
            ]
        ];
        //todo : do this in a template & call it here
        foreach ($peepso_groups_options as $option ): ?>
          <input type="checkbox"
                 name="notification_options[peepso_groups_settings][<?php echo $option['value']  ?>]"
                 value="<?php echo $option['value'] ?>"
                <?php checked(isset($options['peepso_groups_settings'][$option['value']])); ?> />
            <?php echo $option['title'] ;?>
            <br />
        <?php
endforeach;
    }
}
