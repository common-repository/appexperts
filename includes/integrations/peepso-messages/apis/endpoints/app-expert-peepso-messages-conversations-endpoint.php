<?php


class App_Expert_Peepso_Messages_Conversations_Endpoint {
    public function get(WP_REST_Request $request){
        $chatData=[];
        $owner = get_current_user_id();
        $page  =  $request->get_param('page')??1;
		$per_page = $request->get_param('per_page')??10;
        
        $unread_only = $request->get_param('unread_only')??false;
        $peepso_messages = PeepSoMessages::get_instance();
		$model = $peepso_messages->_messages_model;

		$page = max(1, $page) - 1;

		$offset = ceil($per_page * $page);
        $query=$request->get_param('search')??null;

		// perform the search
		$msgs=$model->get_messages('inbox', $owner, $per_page, $offset, $query, $unread_only);
        
        if(!empty($msgs)) {
            foreach ($msgs as $key => $msg) {
                $chatData[] = (new App_Expert_Conversations_Serializer($msg))->get();
            }
        }
        $unread_notes = PeepSoMessageRecipients::get_instance()->get_unread_messages_count(get_current_user_id());
		return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,["unread_conversations_count"=>$unread_notes,"conversations"=>$chatData] );
       
    }
}