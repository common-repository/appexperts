<?php

class App_Expert_Peepso_Messages_Notifications_Helper{

	public static function add_new_message_notification($from_id,$msg_id,$message) {

		$SenderUser  = PeepSoUser::get_instance($from_id);
        $sender = (new App_Expert_User_Serializer($SenderUser))->get();
		$msg=get_post($msg_id);
		$chatData = (new App_Expert_Messages_Serializer($msg))->get();
        $domain = 'app-expert';
        $_langs = App_Expert_Language::get_active_languages();
		$conv_id=wp_get_post_parent_id($msg_id)==0?$msg_id:wp_get_post_parent_id($msg_id);
		$message_data=App_Expert_Peepso_Messages_Messages_Helper::get_last_message_in_conversation($conv_id);
		$conversation=$message_data?(new App_Expert_Conversations_Serializer($message_data))->get():null;
		if($conversation)
			$conversation['conv_viewed']="0";
		//get users tokens to
		$user_tokens=App_Expert_Peepso_Messages_Notifications_Helper::get_user_notification_tokens($conv_id,true);
		$title=[];
		$content=[];
        foreach ($_langs as $lang=>$obj){
            App_Expert_Language::switch_lang($lang);
            $title[$lang]   = translate('New Message'  , $domain);
			$content[$lang]= $sender["user_fullname"] .' '.$message;
        }
		$data=[
            "title" => json_encode($title),
            "content" => json_encode($content),
            "target" => null,
            "segment" => "",
            "attachment_id" => null,
            "type" => 'new_messages_requests',
            "object_id" => null,
            "created_at" => gmdate( 'Y-m-d H:i:s' ),
			"sender" => $sender,
			"extra_data" => [
				"message"=>$chatData,
				"conversation"=>$conversation
			]
        ];
		App_Expert_Notification_Helper::send_push_to_users_by_tokens($data,$user_tokens['muted_notification'],true);
		App_Expert_Notification_Helper::send_push_to_users_by_tokens($data,$user_tokens['notification']);	
	}
	public static function add_read_message_notification($from_id,$conv_id,$user_tokens) {

		$SenderUser  = PeepSoUser::get_instance($from_id);
        $sender = (new App_Expert_User_Serializer($SenderUser))->get();
        $message_data=App_Expert_Peepso_Messages_Messages_Helper::get_last_message_in_conversation($conv_id);
		$conversation=$message_data?(new App_Expert_Conversations_Serializer($message_data))->get():null;
		$data=[
            "title" => '',
            "content" => '',
            "target" => null,
            "segment" => "",
            "attachment_id" => null,
            "type" => 'read_messages_requests',
            "object_id" => null,
            "created_at" => gmdate( 'Y-m-d H:i:s' ),
			"sender" => $sender,
			"extra_data" => [
				"conversation"=>$conversation,
			]
        ];
		App_Expert_Notification_Helper::send_push_to_users_by_tokens($data,$user_tokens,true);
	}

	public static function get_user_notification_tokens($msg_id,$check_mute=null) {
        $tokens=[];
		$tokens['muted_notification']=[];
		$tokens['notification']=[];
		$peepso_participants = new PeepSoMessageParticipants();
		$conv_id=wp_get_post_parent_id($msg_id)==0?$msg_id:wp_get_post_parent_id($msg_id);
		$participants = $peepso_participants->get_participants($conv_id,get_current_user_id());
		$participants=array_diff($participants,[get_current_user_id()]);
		
		foreach($participants as $participant_id)
		{
            $user_tokens=get_user_meta((int) $participant_id,NOTIFICATION_TOKEN_META,true);
			if(!$user_tokens)continue;
			$mayfly = "msgso_mute_{$participant_id}_{$conv_id}";
			if (PeepSo3_Mayfly::get($mayfly) && $check_mute) {
				$tokens=self::set_user_tokens($user_tokens,$tokens,'muted_notification');
			}else
			{
				$tokens=self::set_user_tokens($user_tokens,$tokens,'notification');
			}
		}
        if($tokens&&count($tokens)) return $tokens;
	}
	public static function set_user_tokens($user_tokens,$tokens,$type){
		if(is_array($user_tokens))
		{
			$tokens[$type]= array_merge($tokens[$type],$user_tokens);
		}
		else {
			$tokens[$type][]=$user_tokens;
		}
		return $tokens;
	}
}