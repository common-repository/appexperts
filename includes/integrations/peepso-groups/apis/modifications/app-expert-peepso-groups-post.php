<?php

class App_Expert_Peepso_Groups_Post {
    public function __construct(){
        add_filter("ae_peepso_post_object",array($this,'add_user_edit'),10,2);
    }
    public function add_user_edit($result,$peepso_post){
        $group_id = get_post_meta($peepso_post['ID'], 'peepso_group_id', true);
        if($group_id){
            $gb= new PeepSoGroup($group_id);
            $result['group']=(new App_Expert_Group_Serializer($gb))->get();
        }
        return $result;
    }
}
new App_Expert_Peepso_Groups_Post();
