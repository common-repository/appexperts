<?php
class App_Expert_Peepso_Core_Users{

    public function __construct()
    {
        add_filter('ae_peepso_profile_tab_sub_items',array($this,'get_tab_sub_items'),10,4);
    }
    public function get_tab_sub_items($subItems,$tabname,$obj,$profile_url=""){
        if ($tabname === 'followers'){
            $subItems=[
                [
                    'id'=>$tabname."_followers",
                    'label'=>__('Followers', 'peepso-core'),
                    'url'=>$profile_url.'followers',
                ] ,
                [
                    'id'=>$tabname."_following",
                    'label'=>__('Following', 'peepso-core'),
                    'url'=>$profile_url.'followers/following',
                ]
            ];
        }
        return $subItems;
    }

}
new App_Expert_Peepso_Core_Users();