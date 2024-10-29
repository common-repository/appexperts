<?php
class App_Expert_Send_Notification
{
    public $_lang;

    public function __construct()
    {
        add_action( 'publish_post', array($this, 'send_create_post_notification'),999);
    }
    function send_create_post_notification($post_id)
    {
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options))return;
        $is_option_exist = array_key_exists('new_blog_post_added',$notification_options['general_settings']);
        $is_sent=get_post_meta($post_id,"_as_notification_sent",true);
        if($is_sent)return;

        if($is_option_exist){
            $_notification = App_Expert_Notification_Helper::save_automatic(
                 'New blog post',
                 'Check out our new post',"post",$post_id);
            if($_notification){
                update_post_meta($post_id,"_as_notification_sent",1);
            }
        }
    }
}
new App_Expert_Send_Notification();
