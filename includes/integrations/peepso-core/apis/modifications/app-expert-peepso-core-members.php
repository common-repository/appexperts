<?php
class App_Expert_Peepso_Core_Members{

    public function __construct()
    {
        add_filter('ae_search_management_data',array($this,'search_members'),10,7);
    }
    public function search_members($data,$request,$search,$limit,$page,$order,$orderby){
        if($request->get_param('peepso_types')==null || !in_array('members',$request->get_param('peepso_types')))
            return $data;
        $_POST['no_html'] = 1 ;
        $_POST['query']=$search;
        $_POST['limit']=$limit;
        $_POST['page']=$page;
        $_POST['order']=$order;
        $_POST['orderby']=$orderby; 
        $users=[];
        $ajax = PeepSoMemberSearch::get_instance();
        $resp = new PeepSoAjaxResponse();

        $ajax->search($resp);
        if($resp->success){
            foreach ($resp->data['no_html_data'] as $u){
                $users[]=(new App_Expert_User_Serializer($u))->get();
            }
        }
        $data['members']=$users;
        return $data;
    }

}
new App_Expert_Peepso_Core_Members();