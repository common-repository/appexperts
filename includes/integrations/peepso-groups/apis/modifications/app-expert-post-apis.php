<?php
class App_Expert_Post_Apis{
   public function __construct(){
       add_filter('ae_peepso_add_post_parameters',array($this,'add_group_id_parameter'),10,1);
       add_filter('ae_peepso_handle_add_post_request',array($this,'handle_group_id_add_post_request'), 10, 2);
       add_filter('ae_peepso_handle_get_post_request',array($this,'handle_group_id_get_post_request'), 10, 1);
    }

    public function add_group_id_parameter($parameters)
    {
        $parameters['group_id' ] =  array(
                'description' => __( 'if the post is in a group.' , 'app-expert' ),
                'required' => false
                );
        return $parameters; 
    }

    public function handle_group_id_add_post_request( $post,$request)
    {

        if(isset($post['group_id'])){

            $_POST['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
        }
        return $post;
    }

    public function handle_group_id_get_post_request($post)
    {
        if(isset($post['group_id'])){
            $_POST['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
        }
        return $post;
    }
}
new App_Expert_Post_Apis();