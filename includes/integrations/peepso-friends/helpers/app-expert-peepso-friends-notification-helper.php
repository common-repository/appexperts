<?php
class App_Expert_Peepso_Friends_Notification_helper{
    public static function get_extra_data($_notification){
        $_notification->title = __($_notification->title,'peepso-core');
        $record   = App_Expert_Peepso_Core_Notification_helper::get_notification($_notification->object_id);
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
        $preview = (string)$preview?:"";
        $_notification->content = $preview;
        return $_notification;
    }
}