<?php 
/**
* ======================
* Author : Mahmoud Ramadan. 
* Date :5/20/21.    
* ======================
*/
class App_Expert_User_Serializer {

    protected $user;
    protected $userInfo=[
        "ID" => "get_id",
        "user_nicename" => "get_nicename",
        "user_email" => "get_email",
        "username" => "get_username",
        "user_fullname" => "get_fullname",
        "user_avatar" => "get_avatar",
        "user_cover" => "get_cover",
        "firstname"=>"get_firstname"
    ];

    public function __construct(PeepsoUser $user){
        $this->user=$user;
    }
    public function get(){
        $arr=[];
        foreach($this->userInfo as $key => $value){
            $arr[$key]= $this->user->$value();
        }
        $arr['user_views'] = $this->user->peepso_user['usr_views'];
        $profile_fields = new PeepSoProfileFields($this->user);
        $user_meta_data = $profile_fields->load_fields();
        $arr['profile_fields']=[];
        foreach ($user_meta_data as $d) {
            $value = $d->get_value();
            if(!empty($value)&&in_array(get_class($d),['PeepSoFieldSelectSingle','PeepSoFieldTextDate'])){
                $value =$d->render(false);
            }
            $arr['profile_fields'][] = [
                'label'=>$d->title,
                'value'=>$value,
                'default'=>$d->desc,
            ] ;
        }
        $arr['is_online'] = $this->user->is_online();
        
        $PeepSoUserFollower = new PeepSoUserFollower( $this->user->get_id(),get_current_user_id());
        $arr['is_follow'] = $PeepSoUserFollower->follow;
        $arr['followers_count'] =(string) PeepSoUserFollower::count_followers( $this->user->get_id(), true);

        return apply_filters("ae_peepso_user_object",$arr, $this->user);
    }
}