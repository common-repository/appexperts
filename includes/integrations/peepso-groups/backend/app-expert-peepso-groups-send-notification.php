<?php
class App_Expert_Peepso_Groups_Send_Notification{

    private $_current_integration;
    private $settings_map = [
        'groups_new_post'                 => ['member_post_in_group'],
        'groups_user_invitation_send'     => ['invite_member'],
        'groups_user_join_request_send'   => ['new_join_request'],
        'groups_rename'                   => ['rename_group'],
        'groups_privacy_change'           => ['change_group_privacy'],
        'groups_publish'                  => ['publish_group'],
        'groups_unpublish'                => ['unpublish_group'],
        'member_post_in_group'            => ['groups_new_post'],
        'groups_user_join'                => ['new_member_added'],
        'user_accept_group_invitation'    => ['user_accept_group_invitation'],
        'groups_user_join_request_accept' => ['user_request_has_been_accepted'],

    ];
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;
        add_action('peepso_action_create_notification_after',array($this,'add_push_notification'),99,1);
        add_filter( "ae_send_push_notification_object"   , array($this,'add_push_notification_additional_params'), 10, 2);
    }
    public function add_push_notification($id){
        $record   = App_Expert_Peepso_Core_Notification_helper::get_notification($id);
        if($record){
            if(!in_array($record->not_type,array_keys($this->settings_map))) return;
            $settingsList = $this->settings_map;
            $settingNames = $settingsList[$record->not_type];
            $notification_options = get_option('notification_options',[]);
            if(empty($notification_options)||empty($notification_options['peepso_groups_settings']))return;
            $is_option_exist = false;
            foreach ($settingNames as $settingName)
            {
                $is_option_exist = array_key_exists($settingName,$notification_options['peepso_groups_settings']);
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
            $_notification = App_Expert_Peepso_Groups_Notification_helper::get_extra_data((object)$_notification);
            $_notification->title = $_notification->sender["user_fullname"]." ".$_notification->title;
            $_notification = (array)$_notification;
        }
        return $_notification;
    }

}