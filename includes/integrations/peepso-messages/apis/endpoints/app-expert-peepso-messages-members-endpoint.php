<?php


class App_Expert_Peepso_Messages_Members_Endpoint {
    
	/**
	 * Checks if the current user is part of the conversation, if not the response will be false.
	 * Else, it returns the users that may be added to the conversation.
	 */
	public function get_available_recipients(WP_REST_Request $request)
	{
		$_peepsomessages = PeepSoMessagesPlugin::get_instance();
		$parent_id = $request->get_param('msg_parent_id')??0;
		
		$keyword = $request->get_param('keyword')??'';
		
		$page = $request->get_param('page')??1;

		$peepso_participants = new PeepSoMessageParticipants();
		$data=array();
		if (0 != $parent_id && FALSE === $peepso_participants->in_conversation(get_current_user_id(), $parent_id)) {
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_FORBIDDEN,[__("you are not part of this conversation",'app-expert')]);
		} else {
			$available_participants = $_peepsomessages->get_available_recipients($parent_id, $keyword, $page, false);
			foreach ($available_participants as $participant) {
				$user = PeepSoUser::get_instance($participant['id']);
				$data[]=(new App_Expert_User_Serializer($user))->get();
			}
			return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $data);
		}
	}
	
	public function add_new_recipients_to_conversation()
	{
		$peepso_participants = new PeepSoMessageParticipants();
		
		if (0 != $_POST['msg_parent_id'] && FALSE === $peepso_participants->in_conversation(get_current_user_id(), $_POST['msg_parent_id'])) {
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_FORBIDDEN,[__("you are not part of this conversation",'app-expert')]);
		}
		$conv_id=App_Expert_Peepso_Messages_Members_Helper::add_participants($_POST['msg_parent_id'],$_POST['recipients']);
		$message =App_Expert_Peepso_Messages_Messages_Helper::get_last_message_in_conversation($conv_id);
		return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['message_id' => $conv_id,'conversation'=>(new App_Expert_Conversations_Serializer($message))->get()]);
	}
	
	public function get_chat_members_list(WP_REST_Request $request)
	{
		$peepso_participants = new PeepSoMessageParticipants();
		$msg_id                 = $request->get_param('msg_parent_id')??0;
		$data=array();
		if (0 != $msg_id && FALSE === $peepso_participants->in_conversation(get_current_user_id(), $msg_id)) {
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_FORBIDDEN,[__("you are not part of this conversation",'app-expert')]);
		}
		
		$participants = $peepso_participants->get_participants($msg_id, get_current_user_id());
		foreach ($participants as $participant_user_id) {
				$user = PeepSoUser::get_instance($participant_user_id);
				$data[]=(new App_Expert_User_Serializer($user))->get();
		}
		return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$data);
	}
	
}