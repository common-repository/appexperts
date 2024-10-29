<?php
class App_Expert_Album_Apis{
    public function __construct(){
       add_filter('ae_peepso_add_album_parameters',array($this,'add_group_id_parameter'),10,1);
       add_filter('ae_peepso_handle_upload_photo_request',array($this,'handle_group_id_upload_photo_request'), 10, 2);
       add_filter('ae_peepso_handle_add_album_request',array($this,'handle_group_id_add_album_request'), 10, 2);
       add_filter('ae_peepso_handle_get_album_request',array($this,'handle_group_id_get_album_request'), 10, 2);
       add_filter('ae_peepso_handle_delete_album_request',array($this,'handle_group_id_delete_album_request'), 10, 2);
       add_filter('ae_peepso_handle_edit_album_request',array($this,'handle_group_id_edit_album_request'), 10, 2);
       add_filter('ae_peepso_handle_add_photos_album_request',array($this,'handle_group_id_add_photos_album_request'), 10, 2);
       add_filter('ae_peepso_handle_get_photos_album_request',array($this,'handle_group_id_get_photos_album_request'), 10, 2);
       add_filter('ae_peepso_handle_get_photos_by_album_album_request',array($this,'handle_group_id_get_photos_by_album_album_request'), 10, 2);
       add_filter('ae_peepso_handle_edit_album_allow_update_privacy',array($this,'handle_edit_album_allow_update_privacy'), 10, 2);
    }

    public function add_group_id_parameter($parameters)
    {
        $parameters['group_id' ] =  array(
                'description' => __( 'if the album is in a group.' , 'app-expert' ),
                'required' => false
                );
        return $parameters; 
    }

    public function handle_group_id_upload_photo_request($post,$request)
    {
        if(isset($post['group_id'])){
            $post['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
        }
        return $post;
    }

    public function handle_group_id_add_album_request($post, $request)
    {
        if($request->has_param('group_id')){
            $post['module_id'] = PeepSoGroupsPlugin::MODULE_ID; 
            $post['privacy'] = PeepSo::ACCESS_PUBLIC;
        }
        return $post;
    }

    public function handle_group_id_get_album_request($data,$request)
    {
        if($request->has_param('group_id')){
            $data['user_id']   = $request->get_param('group_id');
            $data['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
        }
        
        return $data;
    }

    public function handle_group_id_delete_album_request($post,$request)
    {
        if($request->has_param('group_id')){
            $post['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
        }
        return $post;
    }

    public function handle_group_id_edit_album_request($post,$request )
    {
        if($request->has_param('group_id'))
        {
            $post['user_id'] = $request->get_param('group_id');
            $post['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
            $post['privacy'] = PeepSo::ACCESS_PUBLIC;
        }
        return $post;
    }

    public function handle_edit_album_allow_update_privacy($allowed,$request){
        if($request->has_param('group_id'))
        {
           return false;
        }
        return $allowed;
    }
    public function handle_group_id_add_photos_album_request($post,$request )
    {
        if($request->has_param('group_id')){
            $post['group_id'] = $request->get_param('group_id');
            $post['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
        }
        return $post;
    }

    public function handle_group_id_get_photos_album_request($data,$request)
    {
        if($request->has_param('group_id')){
            $data['owner_id']  = $request->get_param('group_id');
            $data['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
        }
        return $data;
    }

    public function handle_group_id_get_photos_by_album_album_request($data,$request)
    {
        if($request->has_param('group_id')){
            $data['owner_id'] = 0;
            $data ['module_id'] = PeepSoGroupsPlugin::MODULE_ID;
        }
        return $data;
    }

}
new App_Expert_Album_Apis();