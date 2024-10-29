<?php
class App_Expert_Peepso_Core_Taggable_Friends_Endpoint{

    public function get(WP_REST_Request $request){
        $user_id = get_current_user_id();

		$profile = PeepSoActivity::get_instance();

		$taggable = array();

		// Get activity participants first, if available
		$act_id = $request->get_param('act_id')??NULL;
		$search_letter = $request->get_param('search_letter')??NULL;
        
		if (!is_null($act_id) && FALSE === is_null($activity = $profile->get_activity_post($act_id))) {
			// add author as default
			$author = PeepSoUser::get_instance($activity->post_author);

			// prevent user from tagged by himself
			if ($author->get_id() !== get_current_user_id()) {
				if(empty($search_letter) || (!empty($search_letter) && str_contains($author->get_fullname(),$search_letter))){
					$taggable[$author->get_id()]=(new App_Expert_User_Serializer($author))->get();
				}
			}

			// if is reply
			if ($activity->post_type == PeepSoActivityStream::CPT_COMMENT) {
				$parent_activity = $profile->get_activity_data($activity->act_comment_object_id, $activity->act_comment_module_id);

				if (is_object($parent_activity)) {

					$parent_post = $profile->get_activity_post($parent_activity->act_id);
					$parent_id = $parent_post->act_external_id;

					// check if parent post is a comment
					if($parent_post->post_type == 'peepso-comment') {
						$comment_activity = $profile->get_activity_data($activity->act_comment_object_id, $activity->act_comment_module_id);
						$post_activity = $profile->get_activity_data($comment_activity->act_comment_object_id, $comment_activity->act_comment_module_id);

						$parent_comment = $profile->get_activity_post($comment_activity->act_id);
						$parent_post = $profile->get_activity_post($post_activity->act_id);
					} 

					if (!in_array($parent_post->post_author, $taggable) && intval($parent_post->post_author) !== get_current_user_id()) {
						$parent_post_author = PeepSoUser::get_instance($parent_post->post_author);
						if(empty($search_letter) || (!empty($search_letter) && str_contains($parent_post_author->get_fullname(),$search_letter))){
							$taggable[$parent_post->post_author]=(new App_Expert_User_Serializer($parent_post_author))->get();
						}
					}
				}
			}

			$users = $profile->get_comment_users($activity->act_external_id, $activity->act_module_id);

			while ($users->have_posts()) {

				$users->next_post();

				// skip if user was already found
				if (in_array($users->post->post_author, $taggable)){
					break;
				}

				$user = PeepSoUser::get_instance($users->post->post_author);

				if (!$user->is_accessible('profile')){
					break;
				}

				if(empty($search_letter) || (!empty($search_letter) && str_contains($user->get_fullname(),$search_letter))){
				$taggable[$user->get_id()]=(new App_Expert_User_Serializer($user))->get();
				}

			}
		}

		// Also get friends if available
		if (class_exists('PeepSoFriendsPlugin')) {
			$peepso_friends = PeepSoFriends::get_instance();

			while ($friend = $peepso_friends->get_next_friend($user_id)) {

				// skip if user was already found
				if (in_array($friend->get_id(), $taggable)) {
					break;
				}

				if (!$friend->is_accessible('profile')){
					break;
				}
				
				if(empty($search_letter) || (!empty($search_letter) && str_contains($friend->get_fullname(),$search_letter))){
					$taggable[$friend->get_id()]=(new App_Expert_User_Serializer($friend))->get();
				}
			}
		}
		
        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, array_values($taggable));
	
    }

}