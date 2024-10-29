<?php
class App_Expert_Peepso_Core_send_Notification{

    private $_current_integration;
    private $settings_map = [
            'wall_post'            => ['post_added','post_updated'],
            'user_comment'         => ['comment_added'],
            'stream_reply_comment' => ['comment_added'],

            'like_post'            => ['like_post','like_comment'],
            'profile_like'         => ['like_profile'],

            'tag'                  => ['mention_in_post'],
            'tag_comment'          => ['mention_in_comment'],
    ];
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;
        add_action('peepso_action_create_notification_after',array($this,'add_push_notification'),99,1);
        add_filter( "ae_send_push_notification_object" , array($this,'add_push_notification_additional_params'), 10, 2);
    }
    public function add_push_notification($id){
        $record   = App_Expert_Peepso_Core_Notification_helper::get_notification($id);

        if($record){
            if(!in_array($record->not_type,array_keys($this->settings_map))) return;
            $settingsList = $this->settings_map;
            $settingNames = $settingsList[$record->not_type];
            $notification_options = get_option('notification_options',[]);
            if(empty($notification_options)||empty($notification_options['peepso_core_settings']))return;
            $is_option_exist = false;
            foreach ($settingNames as $settingName)
            {
                $is_option_exist = array_key_exists($settingName,$notification_options['peepso_core_settings']);
                if($is_option_exist) break;
            }
            if(!$is_option_exist) return;
            $user_id = $record->not_user_id;
            if(!$user_id) return;
            App_Expert_Notification_Helper::save_automatic(
                $record->not_message,
                "",
                $record->not_type, $record->not_id ,
                $user_id,[],
                'peepso-core');
            }
    }

    public function add_push_notification_additional_params($_notification,$lang){
        if(in_array($_notification["type"],array_keys($this->settings_map))) {
            $_notification = App_Expert_Peepso_Core_Notification_helper::get_extra_data((object)$_notification);
            $_notification->title = $_notification->sender["user_fullname"]." ".$_notification->title;
            $_notification = (array)$_notification;
        }
        return $_notification;
    }
}