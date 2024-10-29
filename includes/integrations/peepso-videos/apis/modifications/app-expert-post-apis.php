<?php
class App_Expert_Video_Post_Apis{
   public function __construct(){
        add_filter('ae_peepso_add_post_parameters',array($this,'add_url_parameter'),10,1);
        add_filter('ae_peepso_handle_add_post_request',array($this,'handle_video_audio_url_add_post_request'), 10, 2);
    }

    public function add_url_parameter($parameters)
    {
        $parameters['url' ] =  array(
            'description' => __( 'required if type is audio or video and value = url of the video ot the audio.' , 'app-expert' ),
            'type'        => 'string',
            'required' => false
        );

        $parameters['audio' ] =  array(
            'description' => __( 'required if type is audio and audio is uploaded not a url ' , 'app-expert' ),
            'type'        => 'string',
            'required' => false
        );

        $parameters['audio_title' ] =  array(
            'description' => __( 'audio title for uploaded audio file if type is audio and audio is uploaded not a url ' , 'app-expert' ),
            'type'        => 'string',
            'required' => false
        );

        $parameters['audio_artist' ] =  array(
            'description' => __( 'audio artist for uploaded audio file if type is audio and audio is uploaded not a url ' , 'app-expert' ),
            'type'        => 'string',
            'required' => false
        );

        $parameters['audio_album' ] =  array(
            'description' => __( 'audio album for uploaded audio file if type is audio and audio is uploaded not a url ' , 'app-expert' ),
            'type'        => 'string',
            'required' => false
        );

        $parameters['video' ] =  array(
            'description' => __( 'required if type is video and video is uploaded not a url ' , 'app-expert' ),
            'type'        => 'string',
            'required' => false
        );

        $parameters['video_title' ] =  array(
            'description' => __( 'video title for uploaded video file if type is video and video is uploaded not a url ' , 'app-expert' ),
            'type'        => 'string',
            'required' => false
        );

        return $parameters; 
    }

    public function handle_video_audio_url_add_post_request($post,$request )
    {

        $request_type = $request->get_param('type');
        $types = array('audio','video');

        if(in_array($request_type, $types) && !empty($request->get_param('url'))){
            if($request_type === 'audio'){
                $_POST['accepted_type'] = 'audio';
            }
            $PeepSoVideosAjax = PeepSoVideosAjax::get_instance();
            $validation_resp = new PeepSoAjaxResponse();
            $PeepSoVideosAjax->get_preview($validation_resp);
            if(!$validation_resp->success){
                $error = [];
                foreach ($validation_resp->errors as $txt){
                    $error[] =strip_tags($txt);
                }
                return new WP_Error(
                    'data_not_valid',
                    $error,
                    array(
                        'status' => App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,
                    )
                );
            }
        }
        return $_POST;
    }
}
new App_Expert_Video_Post_Apis();