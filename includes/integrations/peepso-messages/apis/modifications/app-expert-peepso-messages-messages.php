<?php
class App_Expert_Peepso_Messages_Messages{

    public function __construct()
    {
         add_filter('ae_login_user_data_object', array($this, 'send_messages_count_notification'), 10, 3);
    }
    public function send_messages_count_notification($user_data,WP_REST_Request $request,WP_User $user)
    {
        $user_data['unreadMessages'] = PeepSoMessageRecipients::get_instance()->get_unread_messages_count($user->ID);
        return $user_data;
    }

}
new App_Expert_Peepso_Messages_Messages();