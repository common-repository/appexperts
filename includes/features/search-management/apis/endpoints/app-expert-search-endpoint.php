<?php
class App_Expert_search_Endpoint {
    
    public function search( WP_REST_Request $request ) {
        $search=$request->get_param('search')??'';
        $limit=$request->get_param('limit')??3;
        $page=$request->get_param('page')??1;
        $order=$request->get_param('order')??'desc';
        $orderby=$request->get_param('orderby')??'ID';
        $offset = ($page - 1) * $limit;
        $data=[];
        $request->set_param('search',$search);
        $request->set_param('page',$page);
        $request->set_param('order',$order);
        $request->set_param('orderby',$orderby);
        $request->set_param('offset',$offset);
        $request->set_param('per_page',$limit);
        $request->set_param('hide_empty',false);
        if($request->get_param('post_types')!=null)
        {
            $posts=[];
            $post_types=[];
            foreach($request->get_param('post_types') as $post_type){
                if(!empty($post_type))
                {
                    $obj=new App_Expert_CPT_Controller($post_type);
                    $response=$obj->get_items($request);
                    if(!empty($response['data']['items'])){
                        foreach($response['data']['items'] as $post)
                        {
                            $posts[]=$post;
                        }
                    }
                    $post_types[$post_type]=$posts;
                }
                
            }
            $data['post_types']=$post_types;
        }
        if($request->get_param('taxonomy_types')!=null)
        {
            $taxonomies_types=[];
            foreach($request->get_param('taxonomy_types') as $taxonomy){
                
                if(!empty($taxonomy) && get_taxonomy( $taxonomy ))
                {
                    $taxonomies=[];
                    $obj=new App_Expert_Terms_Controller($taxonomy);
                    $response=$obj->get_items($request);
                    if(!empty($response['data']['items'])){
                        foreach($response['data']['items'] as $tax)
                        {
                            $taxonomies[]=$tax;
                        }
                    }
                    $taxonomies_types[$taxonomy]=$taxonomies;
                }
            }
            $data['taxonomy_types']=$taxonomies_types;
        }
        
        $data=apply_filters("ae_search_management_data",$data,$request,$search,$limit,$page,$order,$orderby);

        return App_Expert_Response::success("app_expert_search_success",
            'Search returned sueccssfully',$data
        );
     }



}