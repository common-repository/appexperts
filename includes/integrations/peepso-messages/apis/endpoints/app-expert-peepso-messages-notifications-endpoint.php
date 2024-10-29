<?php


class App_Expert_Peepso_Messages_Notifications_Endpoint {
	public function send_typing_notification()
	{
		if(!isset($_POST['msg_parent_id']) || !isset($_POST['is_typing']))
		{
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,[__("parent message id and is_typing params are required",'app-expert')]);
		}
		$user_tokens=App_Expert_Peepso_Messages_Notifications_Helper::get_user_notification_tokens($_POST['msg_parent_id']);
		$message_data=App_Expert_Peepso_Messages_Messages_Helper::get_last_message_in_conversation($_POST['msg_parent_id']);
		$conversation=$message_data?(new App_Expert_Conversations_Serializer($message_data))->get():null;
		
		$is_typing=false;
		$SenderUser  = PeepSoUser::get_instance(get_current_user_id());
        $sender = (new App_Expert_User_Serializer($SenderUser))->get();
		if($_POST['is_typing']){
			$is_typing=true;
		}
		
		$data=[
            "title" => '',
            "content" => '',
			"target" => null,
            "segment" => "",
            "attachment_id" => null,
            "type" => 'user_typing_requests',
            "object_id" => null,
            "created_at" => gmdate( 'Y-m-d H:i:s' ),
			"sender" => $sender,
			"extra_data" => [
				"is_typing"=>$is_typing,
				"conversation"=>$conversation
			]
        ];

		App_Expert_Notification_Helper::send_push_to_users_by_tokens($data,$user_tokens['notification'],true);
		return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['message'=>__('Typing notification sent successfully','app-expert')]);
	}
	/**
	 * Disable/Enable notification for a given conversation
	 *
	 */
	public function set_message_read_notification()
	{
		$parent_id = $_POST['msg_parent_id']??0;
		$user_id = get_current_user_id();

		$default = intval(PeepSo::get_option('messages_read_notification',1));
		
		$read_notif = $_POST['read_notif']??$default;

		$peepso_participants = new PeepSoMessageParticipants();
		$peepso_participants->read_notification_set($parent_id, $user_id, intval($read_notif));

		return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['message'=>__('read receipts updated successfully','app-expert')]);
	}
}