<?php
class App_Expert_Peepso_Core_Notification_helper{
    public static function get_notification($id){
        global  $wpdb;
        $table = PeepSoNotifications::TABLE;
        $sql   = "SELECT * FROM `{$wpdb->prefix}{$table}` WHERE `not_id`=%d LIMIT 1 ";
        return $wpdb->get_row($wpdb->prepare($sql, $id), OBJECT);
    }
    public static function get_extra_data($_notification){
        $_notification->title = __($_notification->title,'peepso-core');
        $record      = self::get_notification($_notification->object_id);
        if(!$record) return $_notification;
        $PeepSoUser  = PeepSoUser::get_instance($record->not_from_user_id);
        $_notification->sender = (new App_Expert_User_Serializer($PeepSoUser))->get();
        $activities = PeepSoActivity::get_instance();
        if($record){
            switch($_notification->type){
                case 'wall_post':
                case 'tag':
                    $not_activity = $activities->get_activity_post($record->not_act_id);
                    $_notification->object_id = $record->not_act_id;
                    break;
                case 'user_comment':
                case 'tag_comment':
                    $not_activity = $activities->get_activity_post($record->not_act_id);
                    $_notification->object_id = $record->not_act_id;
                    
                    if($not_activity->post_type ==="peepso-comment"){
                        $not_activity = $activities->get_activity_data($not_activity->act_comment_object_id, $not_activity->act_comment_module_id);
                        $_notification->object_id =$not_activity->act_id;
                        
                        $not_activity_post = $activities->get_activity_post($not_activity->act_id);
                        if($not_activity_post->post_type ==="peepso-comment" )
                        {
                            $not_activity = $activities->get_activity_data($not_activity_post->act_comment_object_id, $not_activity_post->act_comment_module_id);
                            $_notification->object_id =$not_activity->act_id;
                        }
                    }
                    break;
                case 'stream_reply_comment':
                    $not_activity = $activities->get_activity_data($record->not_external_id, $record->not_module_id);
                    $_notification->comment_activity_id = $not_activity->act_id;
                    $parent_activity = $activities->get_activity_data($not_activity->act_comment_object_id, $not_activity->act_comment_module_id);
                    $_notification->object_id = $parent_activity->act_id;
                    break;
                case 'like_post':
                    $not_activity = $activities->get_activity_post($record->not_act_id);
                    $_notification->object_id =$not_activity->act_id;
                    if($not_activity->post_type ==="peepso-comment"){
                        $not_activity = $activities->get_activity_data($not_activity->act_comment_object_id, $not_activity->act_comment_module_id);
                        $_notification->object_id =$not_activity->act_id;
                    } else{
                        $args = json_decode($record->not_message_args);
                        if(count($args)) {
                            unset($args[0]);
                            $_notification->title = call_user_func_array("sprintf",array_merge([$_notification->title],$args));
                        }
                    }
            }
        }
        $preview = get_post_meta($record->not_external_id,'peepso_human_friendly', TRUE);
        if(empty($preview)){
            $preview = $not_activity->post_content;
        }
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