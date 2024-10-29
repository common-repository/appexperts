<?php
class App_Expert_Peepso_Groups_Notification_helper{
    public static function get_extra_data($_notification){
        $_notification->title = __($_notification->title,'peepso-core');
        $record      = App_Expert_Peepso_Core_Notification_helper::get_notification($_notification->object_id);
        $PeepSoUser  = PeepSoUser::get_instance($record->not_from_user_id);
        $_notification->sender = (new App_Expert_User_Serializer($PeepSoUser))->get();

        $preview = get_post_meta($record->not_external_id,'peepso_human_friendly', TRUE);
        if(!is_array($preview) && strlen($preview)) {
            $preview = trim(
                truncateHtml($preview,
                    PeepSo::get_option('notification_preview_length',50),
                    PeepSo::get_option('notification_preview_ellipsis','...'),
                    false, FALSE)
            );
        }
        $preview = $preview?:"";
        $_notification->content = $preview;
        if($record){
            switch($_notification->type){
                case 'groups_new_post':
                    $args = json_decode($record->not_message_args);
                    if(count($args)) {
                        unset($args[0]);
                        $_notification->title = call_user_func_array("sprintf",array_merge([$_notification->title],$args));
                    }
                    $activities = PeepSoActivity::get_instance();
                    $activity   = $activities->get_post_object($record->not_external_id);
                    $_notification->object_id = $activity->act_id;
                    break;
                case 'groups_user_join_request_send':
                case 'groups_user_join':
                case 'groups_user_invitation_send':
                case 'groups_user_join_request_accept':
                case 'groups_rename':
                case 'groups_privacy_change':
                    $args = json_decode($record->not_message_args);
                    if(count($args)) {
                        unset($args[0]);
                        $_notification->title = call_user_func_array("sprintf",array_merge([$_notification->title],$args));
                    }
                case 'groups_publish':
                case 'groups_unpublish':
                    $_notification->object_id = $record->not_external_id;
                    break;
            }
        }
        return $_notification;
    }
}