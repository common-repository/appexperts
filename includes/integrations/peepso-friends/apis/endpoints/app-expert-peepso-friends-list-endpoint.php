<?php

class App_Expert_Peepso_Friends_List_Endpoint {
    public function get(WP_REST_Request $request){
        $owner = $request->get_param('user_id')??get_current_user_id();
        $page  =  $request->get_param('page')??1;
        // default limit is 1 (NewScroll)
        $limit  = $request->get_param('limit')??1;
        $offset = ($page - 1) * $limit;
        if ($page < 1) $offset = 0;

        $args=array(
            'offset'    => $offset,
            'number'    => $limit,
        );
        //to work with upgraded friends version
        $friends_model =  PeepSoFriendsModel::get_instance();
        $friends = $friends_model->get_friends($owner, $args);
        $data=[];
        if (count($friends)) {
            foreach ($friends as $friend) {
                $friend = PeepSoUser::get_instance($friend);
                $data[]=(new App_Expert_User_Serializer($friend))->get();
            }
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$data);
        } else {
            $message =  (get_current_user_id() == $owner) ? __('You have no friends yet', 'friendso') : sprintf(__('%s has no friends yet', 'friendso'), PeepSoUser::get_instance($owner)->get_firstname());
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$data);
        }
    }

    public function get_requests(WP_REST_Request $request){
        // .../peepso-friends/templates/friends/pending.php
        $PeepSoFriendsRequests = PeepSoFriendsRequests::get_instance();
        $data=[];
        $type = $request->has_param('type')?$request->get_param('type'):"received";
        switch ($type){
            //redundant code should stay as is
            //the if checks & set the data to a private class parameter :)
            case 'received':
                if ($PeepSoFriendsRequests->has_received_requests(get_current_user_id())) {
                    while ($fr_request = $PeepSoFriendsRequests->get_next_request()) {
                        $friend = PeepSoUser::get_instance($fr_request['freq_user_id']);
                        $fr_request['Friend_data'] = (new App_Expert_User_Serializer($friend))->get();
                        $data[]=$fr_request;
                    }
                }
                break;
            case 'sent' :
                if ($PeepSoFriendsRequests->has_sent_requests(get_current_user_id())) {
                    while ($request = $PeepSoFriendsRequests->get_next_request()) {
                        $friend = PeepSoUser::get_instance($request['freq_friend_id']);
                        $request['Friend_data'] = (new App_Expert_User_Serializer($friend))->get();
                        $data[]=$request;
                    }
                }
                break;
        }
        return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$data);
    }

    public function actions(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $ajax = PeepSoFriendsAjax::get_instance();
        $rep = new PeepSoAjaxResponse();

        $sent_action=$request->get_param('friend_action');
        $actions=[
            'add_friend'=>'send_request',
            'cancel_request'=>'cancel_request',
            'accept_request'=>'accept_request',
            'remove_friend'=>'remove_friend',
        ];
        $action = $actions[$sent_action];
        $ajax->$action($rep);

        $msg=str_replace('_',' ',$sent_action)." done successfully";
        if($rep->success){
            switch ($sent_action){
                case 'add_friend':
                    $msg=__("friend request added successfully",'app-expert');
                    break;
                case 'cancel_request':
                    $msg=__("friend request canceled successfully",'app-expert');
                    break;
                case 'accept_request':
                    $msg=__("friend request accepted successfully",'app-expert');
                    break;
                case 'remove_friend':
                    $msg=__("friend removed successfully",'app-expert');
                    break;
            }
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,['message'=>$msg]);
        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$rep->errors);

    }

}
