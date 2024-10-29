<?php
class App_Expert_Peepso_Core_Post{

    public function __construct()
    {
        add_filter('ae_search_management_data',array($this,'search_posts'),10,7);
    }
    public function search_posts($data,$request,$search,$limit,$page,$order,$orderby){
        if($request->get_param('peepso_types')==null  || !in_array('activity',$request->get_param('peepso_types')))
            return $data;
        $_POST['no_html'] = 1 ;
        $_POST['search']=$search;
        $_POST['limit']=$limit;
        $_POST['page']=$page;
        $_POST['order']=$order;
        $_POST['orderby']=$orderby;        
        $posts=[];
        $PeepSoActivity   = new PeepSoActivity();
        $respajax = new PeepSoAjaxResponse();
        $PeepSoActivity->show_posts_per_page($respajax);
        // var_dump($respajax->data);exit;
        foreach ($respajax->data["no_html_data"] as $post){
            $posts[]= (new App_Expert_Post_Serializer($post))->get();
        }
        $data['activity']=$posts;
        return $data;
    }

}
new App_Expert_Peepso_Core_Post();