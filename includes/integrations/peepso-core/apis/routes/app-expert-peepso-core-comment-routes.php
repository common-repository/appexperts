<?php

class App_Expert_Peepso_Core_Comment_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes(){
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/comments/(?P<act_id>[\d]+)', "App_Expert_Peepso_Core_Comment_Endpoint@get_comments" , $this->get_list_params(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE,  '/comments/add', "App_Expert_Peepso_Core_Comment_Endpoint@add_comment" , $this->get_add_params(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE,  '/comments/add-like', "App_Expert_Peepso_Core_Comment_Endpoint@add_like" , $this->get_add_like_params(), "App_Expert_Auth_Request");
        App_Expert_Route::put(PEEPSO_CORE_API_NAMESPACE,  '/comments/update', "App_Expert_Peepso_Core_Comment_Endpoint@update_comment" , $this->get_update_params(), "App_Expert_Auth_Request");
        App_Expert_Route::delete(PEEPSO_CORE_API_NAMESPACE,  '/comments/delete', "App_Expert_Peepso_Core_Comment_Endpoint@delete_comment" , $this->get_delete_params(), "App_Expert_Auth_Request");
    }

    private function get_add_params()
    {
        return apply_filters('ae_peepso_add_comment_parameters',[
            'act_id'        => array(
                'description' => __( 'activity id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'content'        => array(
                'description' => __( 'content of comment that will be added' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'giphy'        => array(
                'description' => __( 'giphy url' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        ]);
    }

    private function get_list_params()
    {
        return [
            'act_id'        => array(
                'description' => __( 'activity id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'limit'        => array(
                'description' => __( 'number of posts.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'page'        => array(
                'description' => __( 'page number.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            )
        ];
    }

    private function get_update_params()
    {
        return apply_filters('ae_peepso_update_comment_parameters',[
            'post_id'        => array(
                'description' => __( 'post id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'content'        => array(
                'description' => __( 'content of comment that will be added' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            )
        ]);
    }

    private function get_delete_params()
    {
       return [
           'post_id'        => array(
               'description' => __( 'post id' , 'app-expert' ),
               'type'        => 'integer',
               'required' => true
           )
       ];
    }

    private function get_add_like_params()
    {
        return [
            'act_id'        => array(
                'description' => __( 'activity id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )
        ];
    }


}

new App_Expert_Peepso_Core_Comment_Routes();

