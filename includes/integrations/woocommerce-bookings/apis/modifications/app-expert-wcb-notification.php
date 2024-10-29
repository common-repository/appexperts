<?php
class App_Expert_WCB_Notification
{
    public function __construct()
    {
        add_filter('ae_notifications_extra_conditions',array($this,'add_wcb_conditions'));
    }
    public function add_wcb_conditions($_conditions){
        return array_merge($_conditions,[
            "(notification.type ='rate_booking'  and notification_user.id is not null )",
            "(notification.type ='confirm_booking'  and notification_user.id is not null )",
            "(notification.type ='admin_cancel_booking'  and notification_user.id is not null )",
            "(notification.type ='customer_cancel_booking'  and notification_user.id is not null )",
            "(notification.type ='admin_notify_product_bookings'  and notification_user.id is not null )",
            "(notification.type ='booking_reminder'  and notification_user.id is not null )",
        ]);
    }
}
new App_Expert_WCB_Notification();