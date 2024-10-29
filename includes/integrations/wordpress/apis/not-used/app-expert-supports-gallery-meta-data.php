<?php
class App_Expert_Supports_Gallery_Meta_Data
{

    public function __construct()
    {
        add_filter('ae_post_response',[$this,'attach_data']);
    }

    public  function attach_data(WP_REST_Response $response){
        $data = $response->get_data();
        if (!isset($data['id']) || empty($data['id']))
            return $response;

        $data['has_gallery'] = false;
        if (!function_exists('parse_blocks')) {
            return $response;
        }


        $post = get_post($data['id']);
        foreach (parse_blocks($post->post_content) as $index => $block) {
            if ("core/gallery" == $block['blockName']) {
                $data['has_gallery'] = true;
                return $response;
            }
        }
        $response->set_data($data);
        return $response;
    }
}
new App_Expert_Supports_Gallery_Meta_Data();