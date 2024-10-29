<?php

class App_Expert_Peepso_Messages_Conversations_Helper{

    const MAX_MSG_PREVIEW_LEN = 35;

    /**
    * Echoes the avatar URL of the user sending the message or the one receiving it.
    * @param  int $post_author The user ID.
    * @param  int $post_id The message ID.
    */
   public static function get_message_avatar($args)
   {
       $user = PeepSoUser::get_instance($args['post_author']);
       $current_user_id = get_current_user_id();
       $avatars = array();
       $avatar_data=array();
       
        $model = new PeepSoMessagesModel();
        // Get first participant and echo that
        $message_id = intval($args['post_id']);
        $parent_id = $model->get_root_conversation($message_id);

        $peepso_participants = new PeepSoMessageParticipants();
        $participants = $peepso_participants->get_participants($parent_id);
        if (count($participants) > 2) {
            $count=0;
            foreach($participants as $participant){
                $avatar_data['id']=$participant;
                $avatar_data['full_name']=PeepSoUser::get_instance($participant)->get_fullname();
                $avatar_data['first_name']=PeepSoUser::get_instance($participant)->get_firstname();
                $avatar_data['avatar']=PeepSoUser::get_instance($participant)->get_avatar();
                $recipients=App_Expert_Peepso_Messages_Conversations_Helper::get_recipient_name($args,$participant);
                $avatar_data['recipients'] = $recipients;
                $avatars[] = $avatar_data;
                $count++;
            }
        } else {
            foreach ($participants as $participant_user_id) {
                $avatar_data['id']=$participant_user_id;
                $avatar_data['full_name']=PeepSoUser::get_instance($participant_user_id)->get_fullname();
                $avatar_data['first_name']=PeepSoUser::get_instance($participant_user_id)->get_firstname();
                $avatar_data['avatar'] = PeepSoUser::get_instance($participant_user_id)->get_avatar();
                $recipients=App_Expert_Peepso_Messages_Conversations_Helper::get_recipient_name($args,$participant_user_id);
                $avatar_data['recipients'] = $recipients;
                $avatars[] = $avatar_data;
            }
        }
       return $avatars;
   }
   /**
    * Echoes the display name of the user sending the message or the one receiving it.
    * @param  int $post_author The user ID.
    * @param  int $post_id The message ID.
    */
   public static function get_recipient_name($args, $current_user_id = NULL)
   {
       if ($current_user_id === NULL) {
           $current_user_id = get_current_user_id();
       }

       if(isset($args['current_user_id'])) {
           $current_user_id = $args['current_user_id'];
       }

       $post_author = intval($args['post_author']);
       $message_id = intval($args['post_id']);
       $model = new PeepSoMessagesModel();
       $parent_id = $model->get_root_conversation($message_id);

       $peepso_participants = new PeepSoMessageParticipants();
       $participants = $peepso_participants->get_participants($parent_id, $current_user_id);
       
       foreach ($participants as $participant_user_id) {

           if($participant_user_id != $current_user_id) {
               $user = PeepSoUser::get_instance($participant_user_id);
               break;
           }
       }

       $in_conversation = count($participants) - 1;

       if ($in_conversation > 1) {
           $and_others = $in_conversation-1;
           return sprintf(__('%s and %s others', 'app-expert'), $user->get_fullname(), $and_others);
       }
       else {
           if(!isset($user) || !is_object($user)) {
               $user = PeepSoUser::get_instance($post_author);
           }
           return $user->get_fullname();

       }
   }

   public static function get_last_author_name($args)
   {
       $current_user_id = get_current_user_id();

       if(isset($args['current_user_id'])) {
           $current_user_id = $args['current_user_id'];
       }

       $post_author = intval($args['post_author']);
       $message_id = intval($args['post_id']);
       $model = new PeepSoMessagesModel();
       $parent_id = $model->get_root_conversation($message_id);

       $peepso_participants = new PeepSoMessageParticipants();
       $participants = $peepso_participants->get_participants($parent_id, $current_user_id);

       $in_conversation = count($participants) -1;

       if($current_user_id == $post_author) {
           return __('You', 'msgso');
       } else if ($in_conversation > 1) {
           $author = PeepSoUser::get_instance($post_author);
           return $author->get_firstname();
       }
       return '';

   }

   /**
    * Returns the appropriate title for a conversation. Gets the post title if it's the parent message,
    * else it returns the message.
    * @return string
    */
   public static function get_conversation_title($post)
   {
       if (0 == $post->post_parent && FALSE === empty($post->post_title))
           return (strip_tags($post->post_title));
       
       $content = $post->post_content;
       $content = strip_tags($content);
       $content = apply_filters('peepso_remove_shortcodes', $content);
       
       if (strlen($content) > self::MAX_MSG_PREVIEW_LEN) {
            $content = (substr($content, 0, self::MAX_MSG_PREVIEW_LEN)) . '&hellip;';
       }
       
       $content = trim(str_replace(array('<a','</a'), array('<span','</span'), $content));
       
       $user = PeepSoUser::get_instance($post->post_author);
       
       if($content == PeepSoMessagesPlugin::MESSAGE_INLINE_LEFT_CONVERSATION) {
           $content = sprintf(__('left the conversation', 'msgso'), $user->get_fullname());
       }

       if($content == PeepSoMessagesPlugin::MESSAGE_INLINE_NEW_GROUP) {
           $content = sprintf(__('created a new group conversation', 'msgso'), $user->get_fullname());
       }

       if(!strlen($content)) {
           $content = "(no text)";
       }

       return ($content);
   }

   /* display post age
    */
   public static function post_time_age($post_id)
   {
       $config_date_format = get_option('date_format'). ' ' . get_option('time_format');
       $config_absolute_dates = PeepSo::get_option('absolute_dates', 0);


       // GMT post date and current date
       $post_date = get_post_time('U', TRUE,$post_id);
       $curr_date = current_time('timestamp', TRUE);

       // post time & time adjusted to user's timezone
       $post_timestamp_user_offset = $post_date + 3600 * PeepSoUser::get_gmt_offset(get_current_user_id());
       $post_time_user_offset = date("Y-m-d H:i:s", $post_timestamp_user_offset); // #5154 used mysql format for mysql2date

       // scheduled post
       if($post_date > $curr_date) {

           return sprintf(__('Scheduled for %s','peepso-core'),date($config_date_format, $post_timestamp_user_offset));

       } else {

           $use_absolute = FALSE;

           if(0 == $config_absolute_dates) {
               $use_absolute = TRUE;
           } elseif(-1==$config_absolute_dates) {
               $use_absolute = FALSE;
           } elseif($config_absolute_dates > 0) {
               if($config_absolute_dates * 3600 > ($curr_date - $post_timestamp_user_offset)) {
                   $use_absolute = TRUE;
               } else {
                   $use_absolute = FALSE;
               }
           }
           
           if ($use_absolute) {
               $date_time=get_post_datetime($post_id);
               $message_date=$date_time->format('Y-m-d H:i:s');
               $message_age=PeepSoTemplate::time_elapsed($post_date, $curr_date);
               return array('message_date'=>$message_date,'message_age'=>$message_age);
           } else {
               return mysql2date($config_date_format, $post_time_user_offset);
           }
       }
   }
   /**
    * Returns available option menu items for the current conversation.
    *
    * @return string
    */
   public static function get_conversation_options() {
       global $post;

       $model = new PeepSoMessagesModel();
       $parent_id = $model->get_root_conversation($post);

       $post = get_post($parent_id);
       setup_postdata($post);

       $peepso_participants = new PeepSoMessageParticipants();
       $muted = $peepso_participants->mute_get($parent_id, get_current_user_id());

       // #695 @todo : use model or not instead using get_user_meta
       $read_notification = intval(PeepSo::get_option('messages_read_notification',1));
       $notif = $read_notification == 1 ? $peepso_participants->read_notification_get($parent_id, get_current_user_id()) : 0;

       $participants = $peepso_participants->get_participants($parent_id, get_current_user_id());
       $show_blockuser = count($participants) < 3;
       if ($show_blockuser) {
           $current_user_id = get_current_user_id();
           foreach ($participants as $participant_user_id) {
               if ($current_user_id !== intval($participant_user_id)) {
                   $show_blockuser_id = $participant_user_id;
                   break;
               }
           }
       }

       $is_user_blocking_enable = PeepSo::get_option('user_blocking_enable', 0);

       $data = array(
           'parent' => $post,
           'muted' => isset($muted) && $muted ? TRUE : FALSE,
           'read_notification' => isset($read_notification) && $read_notification ? TRUE : FALSE,
           'notif' => isset($notif) && intval($notif) ? TRUE : FALSE,
           'show_blockuser' => $is_user_blocking_enable ? (isset($show_blockuser) && $show_blockuser ? TRUE : FALSE) : FALSE,
           'show_blockuser_id' => isset($show_blockuser_id) ? $show_blockuser_id : null
       );

       return $data;
   }
}