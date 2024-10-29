<?php
class App_Expert_Peepso_Groups_Search{

    public function __construct()
    {
        add_filter('ae_search_management_data',array($this,'search_groups'),10,7);
    }
    public function search_groups( $data,$request,$search,$limit,$page,$order,$orderby){
        if($request->get_param('peepso_types')==null  || !in_array('groups',$request->get_param('peepso_types')))
            return $data;
        $PeepSoGroups  = new PeepSoGroups();
        $offset = ($page - 1) * $limit;
        $allGroupsData = $PeepSoGroups->get_groups($offset, $limit, $orderby , $order, $search );
        $groupsData = [];
        
        foreach($allGroupsData as $groupData){
            $groupsData[]=(new App_Expert_Group_Serializer($groupData))->get();
        }
        $data['groups']=$groupsData;
        return $data;
    }

}
new App_Expert_Peepso_Groups_Search();