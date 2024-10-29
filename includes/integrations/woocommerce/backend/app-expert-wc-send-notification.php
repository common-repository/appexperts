<?php
class App_Expert_WC_send_Notification{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;
        add_action( 'woocommerce_order_status_changed', array($this, 'send_change_order_status_notification'));
        add_action( 'comment_approved_to_unapproved', array($this, 'send_change_product_review_status_notification'),999,1);
        add_action( 'comment_unapproved_to_approved', array($this, 'send_change_product_review_status_notification'),999,1);
        add_action( 'woocommerce_order_note_added', array($this, 'send_add_order_notes_notification'), 10, 2);
    }
    function send_change_order_status_notification($order_id)
    {
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['woocommerce_settings']))return;
        $is_option_exist = array_key_exists('order_status_changed',$notification_options['woocommerce_settings']);
        if($is_option_exist){
            $order=wc_get_order($order_id);
            $user_id = $order->get_user_id();
            if(!$user_id) return;
            App_Expert_Notification_Helper::save_automatic(
                'Order Status changed',
                'Your order number %s status changed to %s',
                "order",$order_id,$user_id,
                [$order->get_order_number(),$order->get_status()]);
        }
    }
    function send_change_product_review_status_notification($comment_id)
    {
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['woocommerce_settings']))return;
        $is_approved_option_exist = array_key_exists('product_review_approved',$notification_options['woocommerce_settings']);
        $is_unapproved_option_exist = array_key_exists('product_review_rejected',$notification_options['woocommerce_settings']);
        if($is_approved_option_exist || $is_unapproved_option_exist){
            $comment = get_comment($comment_id);
            $comment_status = wp_get_comment_status($comment_id);
            if(!in_array($comment_status, ['unapproved', 'approved'])) return;
            $post_id = $comment->comment_post_ID;
            $p=get_post($post_id);
            $user_email = get_comment_author_email($comment_id);
            $user = get_user_by_email($user_email);
            if(!$user) return;
            $user_id = $user->ID;
            App_Expert_Notification_Helper::save_automatic(
                $comment_status === 'approved'?'Order Status changed':'Review Rejected',
                $comment_status === 'approved'?'Your Review got accepted, see it now on product page':'unfortunately. Your review on %s got rejected by admin',
                "review",$post_id,$user_id,
                $comment_status === 'approved'?[]:[$p->post_title]);
        }
    }
    function send_add_order_notes_notification($note_id, $order)
    {

        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['woocommerce_settings']))return;

        $is_customer_note = get_comment_meta($note_id, 'is_customer_note', true);

        if($is_customer_note != 1)return;
        $is_option_exist = array_key_exists('order_note_added',$notification_options['woocommerce_settings']);

        if($is_option_exist){
            $order=wc_get_order($order->ID);
            $user_id = $order->get_user_id();
            if(!$user_id) return;
            App_Expert_Notification_Helper::save_automatic(
                    'Order Updated',
                    'A note is added on order %s',
                    "order_add_note",
                    $order->ID,$user_id,[$order->get_order_number()]);
        }
    }

}