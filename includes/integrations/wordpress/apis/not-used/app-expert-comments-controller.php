<?php
if (!defined('ABSPATH')){
	exit();
}

if (!class_exists('App_Expert_Comments_Controller')){

	class App_Expert_Comments_Controller extends WP_REST_Comments_Controller {

		/**
		 * @var App_Expert_Response_Meta_Data
		 */
		private $app_expert_response_meta_data;

		public function __construct() {
			parent::__construct();
			$this->namespace = APP_EXPERT_API_NAMESPACE;
			$this->rest_base = 'comments';
			$this->app_expert_response_meta_data= new App_Expert_Response_Meta_Data($this->get_response_meta_data());

		}

		public function register_routes() {

			register_rest_route($this->namespace, '/' . $this->rest_base,
				array(
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_items' ),
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'                => $this->get_collection_params(),
					),
					array(
						'methods'             => WP_REST_Server::CREATABLE,
						'callback'            => array( $this, 'create_item' ),
						'args'                => $this->get_endpoint_args_for_item_schema( WP_REST_Server::CREATABLE ),
                        'permission_callback' => '__return_true',
                    ),
					'schema' => array( $this, 'get_public_item_schema' ),
				)
			);

		}

		public function get_items($request){
			if(!$this->is_parent_id_set($request)){
				$this->set_parent_query_param($request , array(0));
			}

			$response =  parent::get_items($request);

			if (is_wp_error($response))
				return $response;

			$response = $this->get_children($response , $request);

			return App_Expert_Response::wp_list_success(
				$response,
				'app_expert_comments_retrieved',
				'retrieved all comments'
			);
		}

		public function create_item( $request ) {
			$user_id = App_Expert_Request::get_user_id($request);
			$request['author'] = $user_id;

			$response = parent::create_item($request);

			if (is_wp_error($response))
				return $response;

			return App_Expert_Response::wp_list_success(
				$response,
				'app_expert_comment_created',
				'Created Comment'
			);

		}

		/**
		 * @param WP_REST_Request $request
		 * @param array $parent_id
		 */
		private function set_parent_query_param(WP_REST_Request &$request , array $parent_id){
			$params = $request->get_query_params();
			$params['parent'] = $parent_id;
			$request->set_query_params($params);
		}

		/**
		 * @param WP_REST_Response $response
		 * @param WP_REST_Request $request
		 *
		 * @return WP_REST_Response
		 */
		private function get_children(WP_REST_Response $response , WP_REST_Request $request){
			$response_data_array = $response->get_data();

			foreach ($response_data_array as &$response_data){
				$response_children = array();
				$this->set_parent_query_param($request , array($response_data['id']));
				$item = $this->get_items($request);
				if (!is_wp_error($item) && !empty($item['data']['items'])){
					$response_children[] = $item;
				}
				$response_data['children'] = $response_children;
			}
			$response->set_data($response_data_array);
			return $response;

		}

		/**
		 * @param WP_REST_Request $request
		 *
		 * @return bool
		 */
		private function is_parent_id_set(WP_REST_Request $request){
			$query_params = $request->get_query_params();

			return isset($query_params['parent']) && !empty($query_params['parent']);
		}

		/**
		 * @return array
		 */
		private function get_response_meta_data(){
			return array(
				new App_Expert_Author_Meta_Data()
			);
		}

	}

}

//$app_expert_comments_controller = new App_Expert_Comments_Controller();
//$app_expert_comments_controller->register_routes();