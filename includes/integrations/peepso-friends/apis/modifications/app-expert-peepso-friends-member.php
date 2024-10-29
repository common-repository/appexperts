<?php

class App_Expert_Peepso_Friends_member {
    public function __construct(){
        add_filter("ae_peepso_user_object",array($this,'add_user_edit'),10,2);
    }
    public function add_user_edit($result,$peepso_user){
        $PeepSoFriendsModel =  PeepSoFriendsModel::get_instance();
        $result['is_friend'] = !!$PeepSoFriendsModel->are_friends(get_current_user_id(), $peepso_user->get_id());
        $result['friend_requests_status'] = (string)PeepSoFriendsRequests::request_status(get_current_user_id(), $peepso_user->get_id());
        $result['friend_requests_id'] =(int) PeepSoFriendsRequests::get_request_id(get_current_user_id(), $peepso_user->get_id());

        return $result;
    }
}
new App_Expert_Peepso_Friends_member();
