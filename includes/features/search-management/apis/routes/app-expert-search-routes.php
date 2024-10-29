<?php
class App_Expert_Search_Routes {
    public function __construct(){
        $this->_register_route();
    }

    public function _register_route() {
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/search-types', "App_Expert_search_Endpoint@search" , $this->get_search_parameters());
    }

    public function get_search_parameters(){
        return [
            'search'        => array(
                'description' => __( 'search text.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'limit'        => array(
                'description' => __('limit.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'taxonomy_types'        => array(
                'description' => __( 'array of taxonomy types like post_tag,category ..Note in response returned array of taxonomy_types keys is the taxonomy type and value is array of objs.' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            ),
            'post_types'        => array(
                'description' => __( 'array of post types like post....Note in response returned array of post_types keys is the post type and value is array of objs  ' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            ),
            'peepso_types'        => array(
                'description' => __( 'array of peepso types(members-groups-activity-chat).' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            ),
            'wc_types'        => array(
                'description' => __( 'array of woocommerce types (product).' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            ),
            'wc_taxonomies'        => array(
                'description' => __( 'array of woocommerce taxonomies (product_cat,product_tag).' , 'app-expert' ),
                'type'        => 'array',
                'required' => false
            ),
            'unread_only'        => array(
                'description' => __( 'in case search in chat get read-only conversations by sending 1.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        ];
    }

}
new App_Expert_Search_Routes();