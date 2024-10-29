<?php
class App_Expert_Post_Types_Controller extends WP_REST_Post_Types_Controller
{

    public function __construct()
    {
        $this->rest_base = 'post-types';
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
        $data = array();
        $args = array();
        if(!empty($request->get_param('show_in_rest')))
        {
            $args = array(
                'public'   => true,
                //'_builtin' => false,
                'show_in_rest' => true
            );
        }
        foreach (get_post_types($args, 'object') as $obj) {
            if (empty($obj->show_in_rest) || array_search($obj->name, REMOVE_POST_TYPES, true) !== FALSE || ('edit' === $request['context'] && !current_user_can($obj->cap->edit_posts))) {
                continue;
            }

            $post_type          = $this->prepare_item_for_response($obj, $request);
            if (is_null($post_type)) {
                continue; // Some required data are missing
            }
            $data[$obj->name] = $this->prepare_response_for_collection($post_type);
        }

        $response =  rest_ensure_response($data);

        if (is_wp_error($response))
            return $response;


        return App_Expert_Response::wp_list_success(
            $response,
            'app_expert_post_types_retrieved',
            'retrieved all post Types'
        );
    }

    /**
     * Prepares a post type object for serialization.
     *
     * @since 4.7.0
     *
     * @param WP_Post_Type    $post_type Post type object.
     * @param WP_REST_Request $request   Full details about the request.
     * @return WP_REST_Response Response object.
     */
    public function prepare_item_for_response($post_type, $request)
    {

        $fields = $this->get_fields_for_response($request);
        $data   = array();


        // Check all required keys exists
        $required_keys = ['name', 'slug', 'rest_base'];
        if (count(array_intersect($required_keys, array_keys($fields))) == count($required_keys)) {
            return null;
        }

        $data['name'] = $post_type->label;
        $data['slug'] = $post_type->name;
        $data['rest_base'] = $post_type->rest_base ? $post_type->rest_base : $post_type->name;
        $data['taxonomies'] = get_object_taxonomies($post_type->name);



        $context = !empty($request['context']) ? $request['context'] : 'view';
        $data    = $this->add_additional_fields_to_object($data, $request);
        $data    = $this->filter_response_by_context($data, $context);

        return apply_filters("ae_post_type_response",rest_ensure_response($data),$request);
    }
    public function get_collection_params() {
        $parent=parent::get_collection_params();
        $parent['show_in_rest']= array(
            'type'              => 'int',
            'description'       => 'allowed value 1 to get only post types in',
        );
        return $parent;
    }
}

$app_expert_types_controller = new App_Expert_Post_Types_Controller();
$app_expert_types_controller->register_routes();
