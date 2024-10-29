<?php
class App_Expert_Peepso_Messages_Conversations{

    public function __construct()
    {
        add_filter('ae_search_management_data',array($this,'search_messages'),10,7);
    }
    public function search_messages( $data,$request,$search,$limit,$page,$order,$orderby)
    {    
        if($request->get_param('peepso_types')==null  || !in_array('chat',$request->get_param('peepso_types')))
            return $data;

        $owner = get_current_user_id();
        $unread_only = $_POST['unread_only']??false;
        $peepso_messages = PeepSoMessages::get_instance();
		$model = $peepso_messages->_messages_model;

		$page = max(1, $page) - 1;

		$offset = ceil($limit * $page);
        $chatData=[];
		// perform the search
		$msgs=$model->get_messages('inbox', $owner, $limit, $offset, $search, $unread_only);
        
        if(!empty($msgs)) {
            foreach ($msgs as $key => $msg) {
                $chatData[] = (new App_Expert_Conversations_Serializer($msg))->get();
            }
        }
        $data['chat']=$chatData;
        return $data;
    }

}
new App_Expert_Peepso_Messages_Conversations();