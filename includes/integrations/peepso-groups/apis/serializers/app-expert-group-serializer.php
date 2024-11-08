<?php 
/**
* ======================
* Author : Mahmoud Ramadan. 
* Date :5/20/21.    
* ======================
*/
class App_Expert_Group_Serializer {

    protected $group;
    protected $groupPublic=[
        "id", "owner_id", "name", "description", "members_count", "date_created"
    ];

    public function __construct(PeepSoGroup $group){
        $this->group=$group;
    }
    public function get($isPublicOnly=true){
        $Arr=[];

        if(!$isPublicOnly){
            $Arr=(array)$this->group;
            unset($Arr[' PeepSoGroup _groupuser']);
        }
        foreach($this->groupPublic as $key){
            $Arr[$key]= $this->group->$key;
        }
        $Arr['privacy'] = $this->group->privacy;
        $Arr['privacy']['icon']= APP_EXPERT_URL."assets/imgs/privacy-group-icons/icon-{$this->group->privacy['id']}.svg";
        $Arr['avatar'] = $this->group->get_avatar_url();
        $Arr['cover'] = $this->group->get_cover_url();
        $PeepSoGroupUser= new PeepSoGroupUser($this->group->id);
        $Arr['is_member']=$PeepSoGroupUser->is_member;
        $Arr['role']=(string)$PeepSoGroupUser->role;
        $Arr['role_l8n']=(string)$PeepSoGroupUser->role_l8n;
        $Arr['actions'] =App_Expert_Peepso_Groups_Action_Helper::getActions($PeepSoGroupUser->get_member_actions());

        $gb_follower =  new PeepSoGroupFollower($this->group->id);
        $actions =$gb_follower->get_follower_actions();
//        $Arr['follower_actions'] =ActionHelper::getActions($actions);
        $Arr['follower_actions'] =$actions&&count($actions)?$actions[0]:[];
        $Arr['follower_data'] =[
            'follow' => $gb_follower->follow,
            'notify' => $gb_follower->notify,
            'email'  => $gb_follower->email
        ];

        return $Arr;
    }
}