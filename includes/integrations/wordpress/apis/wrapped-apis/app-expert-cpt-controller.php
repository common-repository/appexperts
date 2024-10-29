<?php
class App_Expert_CPT_Controller extends WP_REST_Posts_Controller {
    public function __construct($post_type){

        parent::__construct($post_type);
        $this->namespace = APP_EXPERT_API_NAMESPACE;
    }

    public function register_routes() {

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_items' ),
                    'args'                => $this->get_collection_params(),
                    'permission_callback' => '__return_true',
                ),
                'schema' => array( $this, 'get_public_item_schema' ),
            )
        );

        $schema        = $this->get_item_schema();
        $get_item_args = array(
            'context' => $this->get_context_param( array( 'default' => 'view' ) ),
        );
        if ( isset( $schema['properties']['password'] ) ) {
            $get_item_args['password'] = array(
                'description' => __( 'The password for the post if it is password protected.' ),
                'type'        => 'string',
            );
        }
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                'args'   => array(
                    'id' => array(
                        'description' => __( 'Unique identifier for the object.' ),
                        'type'        => 'integer',
                    ),
                    'gallery_page' => array(
                        'description' => __( 'Current page of the collection used for post gallery.' ),
                        'type'        => 'integer',
                    ),
                    'gallery_per_page' => array(
                        'description' => __( 'Maximum number of items to be returned in result set for post gallery, default 20.' ),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array( $this, 'get_item' ),
                    'args'                => $get_item_args,
                    'permission_callback' => '__return_true',
                ),
                'schema' => array( $this, 'get_public_item_schema' ),
            )
        );
    }


    public function get_items($request){
        add_filter("rest_{$this->post_type}_query", [$this, 'filter_posts'],10, 2);
        $response =  parent::get_items($request);
        if (is_wp_error($response)) return $response;

        if($request->has_param('_fields')){
            $response = apply_filters( 'rest_post_dispatch',$response,rest_get_server(),$request);
            $request->set_param('_fields',null);
        }
        return App_Expert_Response::wp_list_success(
            $response,
            "app_expert_{$this->rest_base}_retrieved",
            "retrieved all {$this->rest_base}"
        );
    }

    public function filter_posts($args,$request)
    {    
        $additional_query=[];
        foreach ( App_Expert_Taxonomy_Exclude_Helper::get_post_product_taxonomies() as $taxonomy ) {
            $terms_ids =App_Expert_Taxonomy_Exclude_Helper::get_excluded_terms_ids($taxonomy);
            if(!empty($terms_ids))
            {
                $additional_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'term_id',
                'terms'    => $terms_ids,
                'operator'  => 'NOT IN'
                );
            }
        }
        if (!isset($args['tax_query'])) {
            $args['tax_query'] = [];
        }
        $args['tax_query'] = array_merge($additional_query, $args['tax_query']);
        
        return $args;
    }

    public function get_item($request){
        foreach ( App_Expert_Taxonomy_Exclude_Helper::get_post_product_taxonomies() as $taxonomy ) {
            $terms_ids =App_Expert_Taxonomy_Exclude_Helper::get_excluded_terms_ids($taxonomy);
            if( !empty($terms_ids) && has_term($terms_ids,$taxonomy,$request['id']))
            {
                return $error = new WP_Error(
                    'rest_post_invalid_id',
                    __( 'Invalid post ID.' ),
                    array( 'status' => 404 )
                );
            }
        }
        $response =  parent::get_item($request);

        if (is_wp_error($response))
            return $response;

        return App_Expert_Response::wp_rest_success(
            apply_filters("ae_single_{$this->post_type}_response",$response,$request),
            'app_expert_post_retrieved',
            'retrieved requested post'
        );
    }

    public function prepare_item_for_response( $item, $request )
    {
        $request->set_param('type',$this->post_type);
        $res=parent::prepare_item_for_response($item,$request);
        $res = apply_filters("ae_post_response",$res,$request);
        return apply_filters("ae_object_{$this->post_type}_response",$res,$request);
    }

    public static function _init_all_post_types() {

        $args = array(
            'public'   => true,
            //'_builtin' => false,
            'show_in_rest' => true
        );
        $cpt = get_post_types($args);

        foreach ($cpt as $post_type){
            $class = new App_Expert_CPT_Controller($post_type);
            $class->register_routes();
        }
    }
}
App_Expert_CPT_Controller::_init_all_post_types();