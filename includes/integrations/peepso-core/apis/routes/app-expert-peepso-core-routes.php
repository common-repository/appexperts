<?php

class App_Expert_Peepso_Core_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        // posts routes
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/posts', "App_Expert_Peepso_Core_Post_Endpoint@get" , $this->get_posts_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/posts/(?P<id>[\d]+)'   , "App_Expert_Peepso_Core_Post_Endpoint@get_one"     , $this->get_single_post_parameters());
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts', "App_Expert_Peepso_Core_Post_Endpoint@add" , $this->add_post_parameters(),"App_Expert_Add_Post_Request");
        App_Expert_Route::put(PEEPSO_CORE_API_NAMESPACE, '/posts/edit', "App_Expert_Peepso_Core_Post_Endpoint@edit" , $this->edit_post_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::delete(PEEPSO_CORE_API_NAMESPACE, '/posts/delete', "App_Expert_Peepso_Core_Post_Endpoint@delete" , $this->delete_post_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/pin', "App_Expert_Peepso_Core_Post_Endpoint@pin" , $this->pin_post_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/report', "App_Expert_Peepso_Core_Post_Endpoint@report" , $this->report_post_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/poll/vote', "App_Expert_Peepso_Core_Post_Endpoint@submit_vote" , $this->vote_post_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/poll/vote/change', "App_Expert_Peepso_Core_Post_Endpoint@change_vote" , $this->change_vote_post_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/poll/vote/delete', "App_Expert_Peepso_Core_Post_Endpoint@unvote" , $this->delete_vote_post_parameters(), "App_Expert_Auth_Request");

    }

    public function get_posts_parameters(){
        $parameters = array(
                'profile_id'        => array(
                    'description' => __( 'get posts of which profile.' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
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
                ),
                'pinned'        => array(
                    'description' => __( 'flag to show the pinned posts.' , 'app-expert' ),
                    'type'        => 'boolean',
                    'required' => false
                ),
                'search'        => array(
                    'description' => __( 'txt to search post content' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'search_mode'    => array(
                    'description' => __( 'mode to search post content Available values("any","exact")' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'stream_id'        => array(
                    'description' => __( 'core_community(Community)/core_following(Following)/core_scheduled( All scheduled posts)' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'stream_filter_show_my_posts'        => array(
                    'description' => __( 'flag to include current user posts, values allowed: 0/1 ' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'search_hashtag'        => array(
                    'description' => __( 'hashtag string without "#"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                )

            );
        $parameters = apply_filters('ae_peepso_add_post_parameters', $parameters);
        return $parameters;
    }

    public function add_post_parameters(){
        $parameters = array(
            'profile_id'        => array(
                'description' => __( 'post on which profile.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'content'        => array(
                'description' => __( 'Post text.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'acc'        => array(
                'description' => __( 'privacy Value from the settings.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'mood'        => array(
                'description' => __( 'mood Value from the settings.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'type'        => array(
                'description' => __( 'type of the post Allowed values are(activity, files, photo, audio, video, giphy, poll or post_backgrounds).' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'future'        => array(
                'description' => __( 'to schedule posting.' , 'app-expert' ),
                'type'        => 'date',
                'required' => false
            ),
            'options'        => array(
                'description' => __( 'required if type is poll.' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            ),
            'location'        => array(
                'description' => __( 'add location to the post if allowed. location should have "name","latitude" and "longitude".' , 'app-expert' ),
                'type'        => 'Object',
                'required' => false
            ),
            'preset_id'        => array(
                'description' => __( 'post id of post background get it from app-expert settings api post_background section -> backgrounds array of objects, it is required if type = post_backgrounds' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'text_color'        => array(
                'description' => __( 'text color of post background get it from app-expert settings api post_background section -> backgrounds array of objects, it is required if type = post_backgrounds' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'background'        => array(
                'description' => __( 'image url of post background get it from app-expert settings api post_background section -> backgrounds array of objects, it is required if type = post_backgrounds' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'giphy'        => array(
                'description' => __( 'giphy url , it is required if type = giphy' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'options'        => array(
                'description' => __( 'poll options , it is required if type = poll' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            ),
            'allow_multiple'        => array(
                'description' => __( 'can select on option or more , it is required if type = poll and value = 0 or 1' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            )
        );
        $parameters = apply_filters('ae_peepso_add_post_parameters', $parameters);
        return $parameters;

    }

    public function get_single_post_parameters()
    {
        return array(
            'id' => array(
                'description' => __( 'activity id ', 'app-expert' ),
                'type'        => 'integer',
            )
        );
    }

    public function edit_post_parameters(){
        return  array(
            'act_id'        => array(
                'description' => __( 'Activity id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'post'        => array(
                'description' => __( 'Post text.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'acc'        => array(
                'description' => __( 'privacy Value from the settings.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'location'        => array(
                'description' => __( 'add location to the post if allowed. location should have "name","latitude" and "longitude".' , 'app-expert' ),
                'type'        => 'Object',
                'required' => false
            ),
        );

    }

    public function delete_post_parameters(){
        return  array(
            'post_id'        => array(
                'description' => __( 'Post id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )

        );
    }

    public function pin_post_parameters(){
        return  array(
            'post_id'        => array(
                'description' => __( 'Post id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'pin_status'        => array(
                'description' => __( 'Pin Status in case of not send it, value will be 0 => "un-pinned" 1 => "pinned" ' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            )
        );
    }
    public function report_post_parameters(){
        return  array(
            'act_id'        => array(
                'description' => __( 'activity id to report.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'reason'        => array(
                'description' => __( 'one of reasons in settings.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'reason_desc'        => array(
                'description' => __( 'Report description.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
        );
    }

    public function vote_post_parameters(){
        return  array(
            'user_id'        => array(
                'description' => __( 'user_id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'poll_id'        => array(
                'description' => __( 'post id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'polls'        => array(
                'description' => __( 'array of pre-defined options ' , 'app-expert' ),
                'type'        => 'array',
                'required' => true
            ),
        );
    }

    public function change_vote_post_parameters(){
        return  array(
            'user_id'        => array(
                'description' => __( 'user_id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'poll_id'        => array(
                'description' => __( 'post id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )
        );
    }

    public function delete_vote_post_parameters(){
        return  array(
            'user_id'        => array(
                'description' => __( 'user_id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'poll_id'        => array(
                'description' => __( 'post id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )
        );
    }

}

new App_Expert_Peepso_Core_Routes();

