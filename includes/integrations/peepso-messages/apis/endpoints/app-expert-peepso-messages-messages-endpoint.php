<?php


class App_Expert_Peepso_Messages_Messages_Endpoint {

    public function get_messages(WP_REST_Request $request){
       
        $PeepSoMessages = PeepSoMessages::get_instance();
        

		$msg_id                 = $request->get_param('msg_parent_id')??0;
		$from_id                = $request->get_param('from_id')??0;
		$direction              = $request->get_param('direction');
		$user_id                = get_current_user_id();

		// chat related
		$get_participants       = $request->get_param('get_participants')??0;
		// $get_messages           = $request->get_param('get_messages')??1;
		// $get_recently_deleted   = $request->get_param('get_recently_deleted')??0;
		$get_unread             = $request->get_param('get_unread')??0;
		$get_options            = $request->get_param('get_options')??0;

		$conv_users = array();
		$un_read = array();
		$options=array();
		/** Get messages **/
		$ids = array();
		$chat_messages=array();
		
		if ($PeepSoMessages->has_messages_in_conversation(compact('msg_id', 'from_id', 'direction'))) {
			while ($PeepSoMessages->get_next_message_in_conversation()) {

				global $post;
				if($post->post_type != PeepSoMessagesPlugin::CPT_MESSAGE_INLINE_NOTICE || 
				( $post->post_type == PeepSoMessagesPlugin::CPT_MESSAGE_INLINE_NOTICE && $post->post_content =='left'))
				{
					$ids[] = $post->ID;
					$chat_messages[]=$post;
					if (!isset($first)) {
						$first = $post->ID;
					}
				}
			}
		}

		$peepso_participants = new PeepSoMessageParticipants();
		$participants = $peepso_participants->get_participants($msg_id, $user_id);

		// /** Get participants **/
		if( 1 === $get_participants) {
			foreach ($participants as $participant_user_id) {
				if($participant_user_id != $user_id) {
                    $user = PeepSoUser::get_instance($participant_user_id);
					$conv_users[] =$user;
				}
			}
		}
		/** Get unread message count **/
		if( 1 === $get_unread) {
			$unread = 0;
			$no_receipt = FALSE;
			foreach ($participants as $participant_user_id) {
				if ($participant_user_id != $user_id) {
					$receipt = $peepso_participants->read_notification_get($msg_id, $participant_user_id);
					
					if( 0 === (int) $receipt) {
						$no_receipt = TRUE;
						$unread = 0;
						break;
					}
					$unread += PeepSoMessageRecipients::get_unread_messages_count_in_conversation($participant_user_id, $msg_id);
				}
			}
			
			$send_receipt = $peepso_participants->read_notification_get($msg_id, $user_id);
			//num of unread messages in conv for all users
			$un_read['unread']=$unread;
			//false for users when one of members in chat choose not send 
			$un_read['receipt']=(int) $no_receipt ? FALSE : TRUE;
			//for the user who choose not send is 0
			$un_read['send_receipt']=$send_receipt;
			// for read msg normally need unread flag to be 0 and receipt true and send_receipt 1
		}
 
		if (1 === $get_options) {
			$post = get_post($msg_id);
			setup_postdata($post);
			$options=App_Expert_Peepso_Messages_Conversations_Helper::get_conversation_options();
			unset($options['parent']);
		}
		$chatData=[];
		foreach($chat_messages as $message){
			$chatData['messages'][]= (new App_Expert_Messages_Serializer($message,$conv_users,$un_read,$options))->get();
		}

        foreach($conv_users as $user)
        {
            $chatData['participants'][]=(new App_Expert_User_Serializer($user))->get();
        }

        if(!empty($un_read))
        {
            $chatData['unread']=$un_read;
        }
        if(!empty($options))
        {
            $chatData['options']=$options;
        }
		
		return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $chatData);
       
    }
	
	public function mark_read_messages_in_conversation(WP_REST_Request $request)
	{
		$msg_id=$_POST['msg_parent_id']??0;
		$recipients = new PeepSoMessageRecipients();
		$recipients->mark_as_viewed(get_current_user_id(), $msg_id);
		$last_message_data =App_Expert_Peepso_Messages_Messages_Helper::get_last_message_in_conversation($msg_id);
		$conv_read=App_Expert_Peepso_Messages_Messages_Helper::get_message_read_status((int)$last_message_data->ID,$msg_id);
		if($conv_read)
		{
			$user_tokens=App_Expert_Peepso_Messages_Notifications_Helper::get_user_notification_tokens($msg_id);
			App_Expert_Peepso_Messages_Notifications_Helper::add_read_message_notification(get_current_user_id(),$msg_id,$user_tokens['notification']);
		}
		return App_Expert_Peepso_Core_Response_Helper::success_response(
            App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,
            [
                "message"=>__("conversation has been read successfully",'app-expert'),
                'unreadMessages' => PeepSoMessageRecipients::get_instance()->get_unread_messages_count(get_current_user_id())
            ]
        );
	}
	
	public function create_new_message()
	{
		if(!isset($_POST['message']) && !isset($_POST['mood']) && !isset($_POST['giphy']) && !isset($_POST['files']))
		{
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,[__("There is no message content",'app-expert')]);
		}

		$message = htmlspecialchars($_POST['message']??'');
		$peepso_participants = new PeepSoMessageParticipants();
		$model = new PeepSoMessagesModel();
		$creator_id = get_current_user_id();
        
		if(!isset($_POST['msg_parent_id'])){
			
			$msg_id = $model->create_new_conversation($creator_id, $message, '', $_POST['recipients']);
			$conv_id=wp_get_post_parent_id($msg_id)==0?$msg_id:wp_get_post_parent_id($msg_id);
		}
		else{
			if (0 != $_POST['msg_parent_id'] && FALSE === $peepso_participants->in_conversation($creator_id, $_POST['msg_parent_id'])) {
				return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_FORBIDDEN,[__("you are not part of this conversation",'app-expert')]);
			}
			$conv_id=intval($_POST['msg_parent_id']);
			$msg_id = $model->add_to_conversation($creator_id, $conv_id, $message);
		}

		App_Expert_Peepso_Messages_Notifications_Helper::add_new_message_notification($creator_id,$msg_id,$message);
		$message_data =App_Expert_Peepso_Messages_Messages_Helper::get_last_message_in_conversation($conv_id);
		return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['message_id' => $msg_id,'conversation'=>(new App_Expert_Conversations_Serializer($message_data))->get()]);
	}
}