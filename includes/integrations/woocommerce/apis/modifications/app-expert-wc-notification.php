<?php
class App_Expert_WC_Notification
{
    public function __construct()
    {
        add_filter('ae_notifications_extra_conditions',array($this,'add_wc_conditions'));
        add_filter('ae_notification_object',array($this,'add_wc_object_change'));
    }
    public function add_wc_conditions($_conditions){
        return array_merge($_conditions,[
            "(notification.type ='order'  and notification_user.id is not null )",
            "(notification.type ='review' and notification_user.id is not null )",
            "(notification.type ='order_add_note'  and notification_user.id is not null )"
        ]);
    }
    public function add_wc_object_change($notificationObj){
        switch ($notificationObj->type) {
            case "order":
            case "order_add_note":
                $notificationObj->object_name = 'order #'.$notificationObj->object_id;
                break;
            case "review":
                $notificationObj->object_name = get_the_title($notificationObj->object_id);
                break;
        }
        return $notificationObj;
    }
}
new App_Expert_WC_Notification();