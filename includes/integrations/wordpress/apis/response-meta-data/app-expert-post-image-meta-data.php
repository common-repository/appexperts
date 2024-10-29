<?php
class App_Expert_Post_Image_Meta_Data {

    public function __construct()
    {
        add_filter('ae_post_response',[$this,'attach_data']);
    }

    public  function attach_data(WP_REST_Response $response){
        $data = $response->get_data();
        if (!isset($data['id']) || empty($data['id']))
        return $response;

        if (!isset($data['featured_media']) || empty($data['featured_media'])){
            return $response;
        }

        $images = [
            'thumbnail' => get_the_post_thumbnail_url($data['id'], 'thumbnail'),
            'medium'    => get_the_post_thumbnail_url($data['id'], 'medium'),
            'large'     => get_the_post_thumbnail_url($data['id'], 'large'),
            'full'      => get_the_post_thumbnail_url($data['id'], 'full')
        ];

        $data['featured_image'] = $images;
        $response->set_data($data);
        return $response;
    }

}
new App_Expert_Post_Image_Meta_Data();