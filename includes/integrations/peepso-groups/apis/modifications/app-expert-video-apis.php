<?php
class App_Expert_Video_Apis{
    public function __construct(){
       add_filter('ae_peepso_add_video_parameters',array($this,'add_group_id_parameter'),10,1);
       add_filter('ae_peepso_handle_get_video_request',array($this,'handle_group_id_get_video_request'), 10, 2);
    }

    public function add_group_id_parameter($parameters)
    {
        $parameters['group_id' ] =  array(
                'description' => __( 'if the video is in a group.' , 'app-expert' ),
                'required' => false
                );
        return $parameters; 
    }

    public function handle_group_id_get_video_request( $data,$request)
    {
        if($request->has_param('group_id')){
            $owner   = 0;
            $module_id = PeepSoGroupsPlugin::MODULE_ID;
            $data = array_merge($data,array(
                'owner_id' => $owner,
                'module_id' => $module_id
            ));
        }
        return $data;
    }


}
new App_Expert_Video_Apis();