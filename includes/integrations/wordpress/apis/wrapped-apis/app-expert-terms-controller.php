<?php
class App_Expert_Terms_Controller extends WP_REST_Terms_Controller
{
    public function __construct($taxonomy)
    {
        parent::__construct($taxonomy);
        $this->namespace = APP_EXPERT_API_NAMESPACE;

//        $this->app_expert_response_meta_data = new App_Expert_Response_Meta_Data($this->get_response_meta_data());
    }

    public function register_routes()
    {

        register_rest_route($this->namespace, '/' . $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_items'),
                    'args'                => $this->get_collection_params(),
                    'permission_callback' => '__return_true',
                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base . '/(?P<id>[\d]+)',
            array(
                'args'   => array(
                    'id' => array(
                        'description' => __('Unique identifier for the term.'),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => array($this, 'get_item'),
                    'args'                => array(
                        'context' => $this->get_context_param(array('default' => 'view')),
                    ),
                    'permission_callback' => '__return_true',

                ),
                'schema' => array($this, 'get_public_item_schema'),
            )
        );
    }

    public function get_items($request)
    {
        if(!$request->has_param('parent')){
            $request->set_param('parent',0);
        }
        add_filter("rest_{$this->taxonomy}_query", [$this, 'filer_taxonomy'],10, 2);
        $response = parent::get_items($request);
        if (is_wp_error($response))
            return $response;
        return App_Expert_Response::wp_list_success(
            $response,
            'app_expert_categories_retrieved',
            'retrieved all categories'
        );
    }

    public function filer_taxonomy($args,$request)
    {   
        $args['meta_query'] = array(
            array(
               'key'       => App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag,
               'compare' => 'NOT EXISTS'
            ),
            'taxonomy'  => $this->taxonomy
        );
        return $args;
    }

    public function get_item($request)
    {
        $excluded_ids =App_Expert_Taxonomy_Exclude_Helper::get_excluded_terms_ids($this->taxonomy);
        if(in_array($request['id'],$excluded_ids))
        {
            return $error = new WP_Error(
                'rest_term_invalid',
                __( 'Term does not exist.' ),
                array( 'status' => 404 )
            );
        }
        $taxonomy = get_term($request['id'], $this->taxonomy);

        if (is_wp_error($taxonomy))
            return $taxonomy;

        if(!$taxonomy instanceof WP_Term){
            return parent::get_item($request);
        }

        $response = $this->prepare_item_for_response( $taxonomy, $request );

        if (is_wp_error($response))
            return $response;


        return App_Expert_Response::wp_rest_success(
            $response,
            'app_expert_category_retrieved',
            'retrieved requested category'
        );
    }

    /**
     * Prepares a single term output for response.
     *
     * @since 4.7.0
     *
     * @param WP_Term         $item    Term object.
     * @param WP_REST_Request $request Request object.
     * @return WP_REST_Response Response object.
     */
    public function prepare_item_for_response( $item, $request ){
        $res=parent::prepare_item_for_response($item,$request);
        $terms = get_terms(
            array(
                'taxonomy' => $this->taxonomy,
                'parent' => $item->term_id ,
                'hide_empty' => false,
            )
        );
        $children=[];
        if (!empty($terms)){
            foreach ($terms as $term) {
                $child=  $this->prepare_item_for_response( $term, $request );
                $children[]=$child->get_data();
            }
        }
        $res->data['children'] =$children;
        $res = apply_filters("ae_term_response",$res,$request);
        return apply_filters("ae_term_{$this->taxonomy}_response",$res,$request);
    }
    public static function _init_all_taxonomies(){
        $args = array(
            'public'   => true,
            //'_builtin' => false,
            'show_in_rest' => true
        );
        $taxonomies = get_taxonomies($args);
        foreach ($taxonomies as $taxonomy){
            $tax = new self($taxonomy);
            $tax->register_routes();
        }
    }
}
App_Expert_Terms_Controller::_init_all_taxonomies();
