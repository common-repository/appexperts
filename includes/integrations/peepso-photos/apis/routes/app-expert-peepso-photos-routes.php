<?php

class App_Expert_Peepso_Photos_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes(){
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/posts/upload_photo', "App_Expert_Peepso_Photos_Endpoint@upload_photo", $this->upload_post_photo_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/albums', "App_Expert_Peepso_Photos_Endpoint@add" , $this->add_album_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/albums', "App_Expert_Peepso_Photos_Endpoint@get" , $this->get_album_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::delete(PEEPSO_CORE_API_NAMESPACE, '/albums/delete', "App_Expert_Peepso_Photos_Endpoint@delete" , $this->delete_album_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::put(PEEPSO_CORE_API_NAMESPACE, '/albums/edit', "App_Expert_Peepso_Photos_Endpoint@edit" , $this->edit_album_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/albums/add-photos', "App_Expert_Peepso_Photos_Endpoint@add_photos" , $this->add_album_photos_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/albums/photos', "App_Expert_Peepso_Photos_Endpoint@get_photos" , $this->get_photos_parameters(), "App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/albums/(?P<album_id>[\d]+)/photos', "App_Expert_Peepso_Photos_Endpoint@get_album_photos" , $this->get_album_photos_parameters(), "App_Expert_Auth_Request");

    }

    public function upload_post_photo_parameters(){
        $parameters = array(
            'filedata[0]'        => array(
                'description' => __( 'uploaded photo' , 'app-expert' ),
                'type'        => 'file',
                'required' => false
            )
        );
        $parameters = apply_filters('ae_peepso_add_album_parameters', $parameters);
        return $parameters;

    }

    public function get_album_parameters(){
        $parameters = array(
                'user_id'        => array(
                    'description' => __( 'to view user album list' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'limit'        => array(
                    'description' => __( 'number of albums.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'page'        => array(
                    'description' => __( 'page number.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'sort'        => array(
                    'description' => __( 'album sorting and its default value is "desc"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                )

            );
        $parameters = apply_filters('ae_peepso_add_album_parameters', $parameters);
        return $parameters;
    }

    public function add_album_parameters(){
        $parameters = array(
            'name'        => array(
                'description' => __( 'Album name.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'description'        => array(
                'description' => __( 'Album description.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'type'        => array(
                'description' => __( 'Album type. like "album" ' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'photo'        => array(
                'description' => __( 'album photos it is array of uploaded photos it should contain at least one photo' , 'app-expert' ),
                'type'        => 'Array',
                'required' => true
            ),
            'privacy'        => array(
                'description' => __( 'album privacy [public:10, members_only:20, only_me:40]' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        );
        $parameters = apply_filters('ae_peepso_add_album_parameters', $parameters);
        return $parameters;

    }

    public function edit_album_parameters(){
        $parameters = array(
            'album_id'        => array(
                'description' => __( 'ALbum id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'name'        => array(
                'description' => __( 'Album name.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'description'        => array(
                'description' => __( 'Album description.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'privacy'        => array(
                'description' => __( 'album privacy [public:10, members_only:20, only_me:40]' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        );
        $parameters = apply_filters('ae_peepso_add_album_parameters', $parameters);
        return $parameters;

    }

    public function delete_album_parameters(){
        $parameters = array(
            'album_id'        => array(
                'description' => __( 'Album id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )
        );

        $parameters = apply_filters('ae_peepso_add_album_parameters', $parameters);
        return $parameters;
    }

    public function add_album_photos_parameters(){
        $parameters = array(
            'album_id'        => array(
                'description' => __( 'ALbum id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'type'        => array(
                'description' => __( 'type it should be "photo" ' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'photo'        => array(
                'description' => __( 'album photos it is array of uploaded photos it should contain at least one photo' , 'app-expert' ),
                'type'        => 'Array',
                'required' => true
            )
        );
        $parameters = apply_filters('ae_peepso_add_album_parameters', $parameters);
        return $parameters;

    }

    public function get_photos_parameters(){
        $parameters = array(
                'user_id'        => array(
                    'description' => __( 'to view user album list' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'limit'        => array(
                    'description' => __( 'number of albums.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'page'        => array(
                    'description' => __( 'page number.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'sort'        => array(
                    'description' => __( 'album sorting and its default value is "desc"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                )

            );
        $parameters = apply_filters('ae_peepso_add_album_parameters', $parameters);
        return $parameters;
    }

    public function get_album_photos_parameters(){
        $parameters = array(
                'album_id'        => array(
                    'description' => __( 'Album id' , 'app-expert' ),
                    'type'        => 'integer',
                ),
                'user_id'        => array(
                    'description' => __( 'to view user album list' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'limit'        => array(
                    'description' => __( 'number of albums.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'page'        => array(
                    'description' => __( 'page number.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'sort'        => array(
                    'description' => __( 'album sorting and its default value is "desc"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                )

            );
        $parameters = apply_filters('ae_peepso_add_album_parameters', $parameters);
        return $parameters;
    }

}

new App_Expert_Peepso_Photos_Routes();

