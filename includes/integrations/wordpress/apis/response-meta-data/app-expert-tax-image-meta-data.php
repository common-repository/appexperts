<?php
class App_Expert_Tax_Image_Meta_Data {

    public function __construct(){
        add_filter('ae_term_response',[$this,'attach_data']);
    }

    public function attach_data(WP_REST_Response $response){
        $data = $response->get_data();
        if (!isset($data['id']) || empty($data['id']))return $response;


        if (!isset($data['taxonomy']) || empty($data['taxonomy'])) return $response;


        $images = [
            'thumbnail' =>  '',
            'medium' =>  '',
            'large' =>  '',
            'full' =>  ''
        ];

        if (App_Expert_Taxonomy_Image_Helper::is_taxonomy_image_enabled($data['taxonomy'])){
            $images['thumbnail'] = App_Expert_Taxonomy_Image_Helper::get_taxonomy_image_url($data['id'], 'thumbnail')?:'';
            $images['medium']    = App_Expert_Taxonomy_Image_Helper::get_taxonomy_image_url($data['id'], 'medium')?:'';
            $images['large']     = App_Expert_Taxonomy_Image_Helper::get_taxonomy_image_url($data['id'], 'large')?:'';
            $images['full']      = App_Expert_Taxonomy_Image_Helper::get_taxonomy_image_url($data['id'], 'full')?:'';
        }

        $data['taxonomy_image'] = $images;
        $response->set_data($data);
        return $response;
    }

}
new App_Expert_Tax_Image_Meta_Data();