<?php

class App_Expert_Peepso_Messages_Members_Helper{

	/**
	 * Adds participants to a conversation.
	 */
	public static function add_participants($message_parent_id,$new_participants)
	{
		$peepso_participants = new PeepSoMessageParticipants();
		$user_id = get_current_user_id();

		$is_group = $peepso_participants->is_group($message_parent_id);
		
		// if this is not a group conversation, we need to spawn a new one
		if(0 == $is_group) {
			$all_participants = $peepso_participants->get_participants($message_parent_id, $user_id);

			$all_participants = array_merge($all_participants, $new_participants);

			$message = __('Created a new group conversation', 'msgso');

			$model = new PeepSoMessagesModel();
			$message_parent_id = $model->create_new_conversation($user_id, $message, '', $all_participants, array('post_type' => PeepSoMessagesPlugin::CPT_MESSAGE_INLINE_NOTICE));
			App_Expert_Peepso_Messages_Notifications_Helper::add_new_message_notification(get_current_user_id(),$message_parent_id ,$message);
		} else {

			$_participants = array();
			foreach ($new_participants as $participant) {
				$u = PeepSoUser::get_instance($participant);
				$_participants[$participant] = $u->get_fullname();
			};

			$peepso_participants->add_participants($message_parent_id, $new_participants);
		}
		return $message_parent_id;
	}
}