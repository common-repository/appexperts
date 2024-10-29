<?php
class App_Expert_CPT_Term_Meta_Data {
    public function __construct() {
        add_filter('ae_post_response',[$this,'attach_data']);
    }

    public  function attach_data(WP_REST_Response $response){
        $data = $response->get_data();
        $post_type = get_post_type($data['id']);

        $taxonomies = wp_list_filter( get_object_taxonomies( $post_type, 'objects' ), array( 'show_in_rest' => true ) );

        foreach ($taxonomies as $taxonomy){
            //need to check for rest_base and name because some custom taxonomies don't have rest_base
            $tax_rest_name = '';


            if (isset($data[$taxonomy->name]) && !empty($data[$taxonomy->name]))
                $tax_rest_name = $taxonomy->name;

            if (isset($data[$taxonomy->rest_base]) && !empty($data[$taxonomy->rest_base]))
                $tax_rest_name = $taxonomy->rest_base;

            if (empty($tax_rest_name))
                continue;

            $data[$tax_rest_name] = $this->get_term_data($taxonomy->name , $data[$tax_rest_name]);
        }
        $response->set_data($data);
        return $response;
    }

    public function get_term_data(string $term_name , array $data){
        return get_terms(
            array(
                'taxonomy' => $term_name,
                'include' => $data
            )
        );
    }
}
new App_Expert_CPT_Term_Meta_Data();