<?php


class App_Expert_Peepso_Messages_Actions_Endpoint {
	public function leave_conversation()
	{
		$peepso_participants = new PeepSoMessageParticipants();
		
		if(!isset($_POST['msg_parent_id']))
		{
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,['message'=>__("parent message id param is required",'app-expert')]);
		}

		if (0 != $_POST['msg_parent_id'] && FALSE === $peepso_participants->in_conversation(get_current_user_id(), $_POST['msg_parent_id'])) {
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_FORBIDDEN,['message'=>__("you are not part of this conversation",'app-expert')]);
		}
		$msg_id=intval($_POST['msg_parent_id']);
		$model = new PeepSoMessagesModel();
		$parent_id = $model->get_root_conversation($msg_id);
		$user = PeepSoUser::get_instance(get_current_user_id());

		// Create the inline notification
		$new_msg_id=$model->add_inline_notification(get_current_user_id(), $parent_id, PeepSoMessagesPlugin::MESSAGE_INLINE_LEFT_CONVERSATION);
	
		if(!$new_msg_id)
		{
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,['message'=>__("Error while sending left message",'app-expert')]);
		}
		
		$message=sprintf(__('%s left the conversation', 'msgso'), $user->get_fullname());
		App_Expert_Peepso_Messages_Notifications_Helper::add_new_message_notification(get_current_user_id(),$new_msg_id,$message);
		$peepso_participants->remove_participant(get_current_user_id(),$msg_id );
		return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['message'=>__('left the conversation successfully','app-expert')]);
	}
	
	public function mute_conversation()
	{
		$peepso_participants = new PeepSoMessageParticipants();
		
		if(!isset($_POST['msg_parent_id']) || !isset($_POST['mute_period']))
		{
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,['message'=>__("parent message id and mute period params are required",'app-expert')]);
		}

		if (0 != $_POST['msg_parent_id'] && FALSE === $peepso_participants->in_conversation(get_current_user_id(), $_POST['msg_parent_id'])) {
			return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_FORBIDDEN,['message'=>__("you are not part of this conversation",'app-expert')]);
		}
		// parameter is in hours, we need seconds
		$mute_period = intval( 60 * 60 * $_POST['mute_period'] );
		$parent_id = $_POST['msg_parent_id'];

		$user_id = get_current_user_id();

		$participants = new PeepSoMessageParticipants();
		$participants->mute_set($parent_id, $user_id, $mute_period);

		return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['message'=>__('muted the conversation successfully','app-expert')]);
	}
	
}