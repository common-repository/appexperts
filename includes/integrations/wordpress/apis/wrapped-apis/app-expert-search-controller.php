<?php
class App_Expert_Search_Controller extends WP_REST_Search_Controller
{

    public function __construct()
    {
        $search_handlers = array(new WP_REST_Post_Search_Handler());
        parent::__construct($search_handlers);
        $this->rest_base = 'search';
        $this->namespace = APP_EXPERT_API_NAMESPACE;
    }


    public function register_routes()
    {

        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
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
    }

    public function get_items($request)
    {
        $response = parent::get_items($request);

        if (is_wp_error($response))
            return $response;

        return App_Expert_Response::wp_list_success(
            $response,
            'app_expert_search_retrieved',
            'retrieved all search results'
        );
    }

    public function prepare_item_for_response($post_id, $request)
    {

        $response = parent::prepare_item_for_response($post_id, $request);
        $fields   = $this->get_fields_for_response($request);
        $data     = $response->get_data();

        // Add additional fields
        $post = get_post($post_id);

        if (!in_array('featured_image', $fields, true)) {
            $data['featured_image'] = get_the_post_thumbnail_url($post_id, 'thumbnail');
        }

        // Post type data
        $post_type = get_post_type_object($post->post_type);
        if (!in_array('rest_base', $fields, true)) {
            $data['rest_base'] = $post_type->rest_base ? $post_type->rest_base : $post_type->name;
        }

        $context = !empty($request['context']) ? $request['context'] : 'view';
        //commented before restructure
        // $data = $this->filter_response_by_context($data, $context);

        // Wrap the data in a response object.
        return apply_filters("ae_post_search_response",rest_ensure_response($data),$request);
    }
}


$app_expert_search_controller = new App_Expert_Search_Controller();
$app_expert_search_controller->register_routes();
