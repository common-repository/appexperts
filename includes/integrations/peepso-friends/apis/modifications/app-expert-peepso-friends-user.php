<?php
class App_Expert_Peepso_Friends_Users{

    public function __construct()
    {
        add_filter('ae_login_user_data_object', array($this, 'add_auth_changes'), 10, 3);
        add_filter('ae_peepso_profile_tab_sub_items',array($this,'get_tab_sub_items'),10,4);
    }
    public function get_tab_sub_items($subItems,$tabname,$obj,$profile_url=""){
        if ($tabname === 'friends'){
            $friends_requests = PeepSoFriendsRequests::get_instance();
            $count =count($friends_requests->get_received_requests());
            $subItems=[
                [
                    'id'=>$tabname."_friends",
                    'label'=>__('Friends', 'friendso'),
                    'url'=>$profile_url.'friends',
                ]
            ];
            if($obj->get_id()==get_current_user_id()){
                    $subItems[]= [
                        'id'=>$tabname."_requests",
                        'label'=>__('Friend requests', 'friendso'),
                        'count'=>(int)$count,
                        'url'=>$profile_url.'requests',
                    ];
                }
        }
        return $subItems;
    }
    public function add_auth_changes($user_data,WP_REST_Request $request,WP_User $user){
        $friends_requests = PeepSoFriendsRequests::get_instance();
        $user_data['friends_count'] =  count($friends_requests->get_received_requests($user->ID));
        return $user_data;
    }

}
new App_Expert_Peepso_Friends_Users();