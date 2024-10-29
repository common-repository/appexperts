<?php 
/**
* ======================
* Author : Mahmoud Ramadan. 
* Date :6/8/21.    
* ======================
*/
class App_Expert_Member_Serializer {

    protected $user;
    protected $memberKeys=[
        "user_id", "group_id", "role", "role_l8n", "joined_date"
    ];

    public function __construct(PeepSoGroupUser $user){
        $this->user=$user;
    }
    public function get(){

        $Arr=[];
        foreach($this->memberKeys as $key){
            $Arr[$key]= $this->user->$key;
        }
        $Arr['role_l8n'] = (string)$Arr['role_l8n'];
        $Arr['role'] = (string)$Arr['role'];

        $PeepSoUser  = PeepSoUser::get_instance($this->user->user_id);
        $userData=(new App_Expert_User_Serializer($PeepSoUser))->get();
        $Arr['personal_data'] = $userData;
        $group_current_user = new PeepSoGroupUser($this->user->group_id);
        $group_passive_user = new PeepSoGroupUser($this->user->group_id,  $this->user->user_id);
        $Arr['actions_passive'] = App_Expert_Peepso_Groups_Action_Helper::getActions($group_passive_user->get_member_passive_actions($group_current_user->get_manage_user_rights()));

        return $Arr;
    }
}