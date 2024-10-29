<?php

class App_Expert_Peepso_Core_Notification {
    public $notification_types = [
        'wall_post',
        'user_comment',
        'stream_reply_comment',
        'like_post',
        'profile_like',
        'tag',
        'tag_comment'
    ];

    public function __construct() {
        add_filter( "ae_notification_object", array($this,'add_additional_params'), 10, 1 );
        add_filter( "ae_notifications_extra_conditions", array($this,'add_conditions'), 10, 1 );
        add_action("ae_notification_after_mark_as_seen", array($this,'mark_peepso_notification_as_seen'));
    }

    public function add_additional_params($_notification){
        if(in_array($_notification->type,$this->notification_types)){
            $_notification = App_Expert_Peepso_Core_Notification_helper::get_extra_data($_notification);
        }
        return $_notification;
    }

    public function add_conditions($_conditions){
        $peepso_core_conditions =[];
        foreach ($this->notification_types as $type){
            $peepso_core_conditions[]= "(notification.type ='$type'              and notification_user.id is not null )";
        }
       return array_merge($_conditions,$peepso_core_conditions);
    }
    public function mark_peepso_notification_as_seen($_notification){
        if(in_array($_notification->type,$this->notification_types)){
           $not_id= $_notification->object_id ;
            $note = new PeepSoNotifications();
            $note->mark_as_read(get_current_user_id(), $not_id);
        }
    }
}
new App_Expert_Peepso_Core_Notification();