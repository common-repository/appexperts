<?php
class App_Expert_WCB_send_Notification{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;
        add_action( 'woocommerce_booking_complete', array($this,'send_notification_add_rate'), 10, 2);
        add_action( 'woocommerce_booking_confirmed', array($this,'send_notification_booking_confirmed'), 10, 2);
        add_action( 'woocommerce_booking_cancelled', array($this,'send_notification_admin_cancel_booking'), 20, 2);
        add_action( 'wc_bookings_notification_sent', array($this,'send_notification_admin_notify_product_bookings'), 10, 2);
        add_action( 'wc-booking-reminder', array( $this, 'send_notification_booking_reminder' ) );
    }

    public function send_notification_add_rate($booking_id, $booking)
    {
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['booking_settings']))return;

        $is_option_exist = array_key_exists('complete_and_rate_booking',$notification_options['booking_settings']);
        $is_translated_booking = get_post_meta($booking_id,"_booking_duplicate_of",true);
        if($is_option_exist && !$is_translated_booking){
            $is_sent=get_post_meta($booking_id,"_as_notification_sent",true);
            if($is_sent)return;
            $user_id = $booking->customer_id;
            if(!$user_id) return;
            App_Expert_Notification_Helper::save_automatic(
                'Booking Completed',
                'Please rate your booking',
                "rate_booking",$booking_id,$user_id);
        }
    }

    public function send_notification_booking_confirmed($booking_id, $booking)
    {
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['booking_settings']))return;

        $is_option_exist = array_key_exists('booking_confirmed',$notification_options['booking_settings']);
        $is_translated_booking = get_post_meta($booking_id,"_booking_duplicate_of",true);
        if($is_option_exist && !$is_translated_booking){
            $is_sent=get_post_meta($booking_id,"_as_notification_sent",true);
            if($is_sent)return;
            $user_id = $booking->customer_id;
            if(!$user_id) return;
            App_Expert_Notification_Helper::save_automatic(
                'Booking confirmed',
                'Your order number %s Confirmed',
                "confirm_booking",$booking_id,$user_id,
                [$booking_id]);
        }
    }

    public function send_notification_admin_cancel_booking($booking_id, $booking)
    {
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['booking_settings']))return;

        $is_admin_option_exist = array_key_exists('admin_cancel_booking',$notification_options['booking_settings']);
        $customer_cancel_booking = get_post_meta($booking_id,"customer_cancel_booking",true);
        $is_translated_booking = get_post_meta($booking_id,"_booking_duplicate_of",true);

        if($is_admin_option_exist && !$customer_cancel_booking && !$is_translated_booking){
            $is_sent=get_post_meta($booking_id,"_as_notification_sent",true);
            if($is_sent)return;

            $user_id = $booking->customer_id;
            if(!$user_id) return;
            App_Expert_Notification_Helper::save_automatic(
                'Booking cancelled',
                'Your Booking number %s cancelled by Our team',
                "admin_cancel_booking",$booking_id,$user_id,
                [$booking_id]);
        }
    }

    public function send_notification_admin_notify_product_bookings($bookings, $notification)
    {

        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['booking_settings']))return;

        $is_option_exist = array_key_exists('booking_manual_notification',$notification_options['booking_settings']);
        if($is_option_exist){
            $notification_subject    = wc_clean( stripslashes( $notification->subject ) );
            $notification_message    = wp_kses_post( stripslashes( $notification->notification_message ) );

            // because $bookings only contain one booking object
            if(gettype($bookings) == 'object'){
                $bookings =[$bookings];
            }
            foreach($bookings as $booking){
                $user_id = $booking->customer_id;
                if(!$user_id) continue;
                $booking_id = $booking->ID;
                $is_translated_booking = get_post_meta($booking_id,"_booking_duplicate_of",true);
                if(!$is_translated_booking){
                    $is_sent=get_post_meta($booking_id,"_as_notification_sent",true);
                    if($is_sent) continue;
                    App_Expert_Notification_Helper::save_automatic(
                        $notification_subject,
                        $notification_message,
                        "admin_notify_product_bookings",$booking_id,$user_id);
                }
            }
        }
    }

    public function send_notification_booking_reminder($booking_id)
    {
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['booking_settings']))return;

        $is_option_exist = array_key_exists('booking_reminder',$notification_options['booking_settings']);
        $is_translated_booking = get_post_meta($booking_id,"_booking_duplicate_of",true);
        if($is_option_exist && !$is_translated_booking){
            $is_sent=get_post_meta($booking_id,"_as_notification_sent",true);
            if($is_sent)return;
            $booking = get_wc_booking($booking_id);
            $time = date('Y-m-d h:i A',$booking->get_start());
            $user_id = $booking->customer_id;
            if(!$user_id) return;
            App_Expert_Notification_Helper::save_automatic(
                'Booking Reminder',
                'Reminder your Booking number %s in %s',
                "booking_reminder",$booking_id,$user_id,
                [$booking_id, $time]);
        }
    }

}