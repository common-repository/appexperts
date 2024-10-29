<?php

class App_Expert_Peepso_Videos_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/upload_video', "App_Expert_Peepso_Videos_Endpoint@upload_video", $this->upload_post_video_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/videos', "App_Expert_Peepso_Videos_Endpoint@get", $this->get_video_parameters(), "App_Expert_Auth_Request");
    }

    public function upload_post_video_parameters(){
        return  array(
            'filedata'        => array(
                'description' => __( 'video file' , 'app-expert' ),
                'type'        => 'file',
                'required' => false
            ),
            'is_audio'        => array(
                'description' => __( 'equal 1 if uploaded file is audio file and it is require in this case' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            )
        );
    }

    public function get_video_parameters(){
        $parameters = array(
                'user_id'        => array(
                    'description' => __( 'to view user video list' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'limit'        => array(
                    'description' => __( 'number of videos.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'page'        => array(
                    'description' => __( 'page number.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'sort'        => array(
                    'description' => __( 'video sorting and its default value is "desc"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'media_type'        => array(
                    'description' => __( 'media type ("audio","video","all")& default:"all"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                )

            );
        $parameters = apply_filters('ae_peepso_add_video_parameters', $parameters);
        return $parameters;
    }

}

new App_Expert_Peepso_Videos_Routes();

