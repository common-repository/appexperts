<?php
class App_Expert_Tax_Post_Types_Meta_Data
{

    public function __construct()
    {
        add_filter('ae_term_response',[$this,'attach_data']);
    }

    public  function attach_data(WP_REST_Response $response)
    {
        $data = $response->get_data();
        if (!isset($data['id']) || empty($data['id'])) return $response;


        if (!isset($data['taxonomy']) || empty($data['taxonomy'])) return $response;

        global $wp_taxonomies;
        $post_types = (isset( $wp_taxonomies[$data['taxonomy']] ) ) ? $wp_taxonomies[$data['taxonomy']]->object_type : [];
        $post_types = array_combine($post_types, $post_types);
        $post_types_data = get_post_types([], 'object');
        $post_types_data = array_intersect_key($post_types_data, $post_types);
        $required_post_type_data = [];

        foreach($post_types_data as $post_type){
            $needed_data = [
                'name' => $post_type->label,
                'slug' => $post_type->name,
                'rest_base' => $post_type->rest_base ? $post_type->rest_base : $post_type->name
            ];
            $required_post_type_data[] = $needed_data;
        }


        $data['taxonomy_post_types'] = $required_post_type_data;
        $response->set_data($data);
        return $response;
    }
}
new App_Expert_Tax_Post_Types_Meta_Data();