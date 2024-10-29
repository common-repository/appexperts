<?php
if (!defined('ABSPATH')){
	exit();
}

if (!class_exists('App_Expert_Tags_Controller')){

	class App_Expert_Tags_Controller extends WP_REST_Terms_Controller {


		/**
		 * @var App_Expert_Hierarchy_Handler
		 */
		public $app_expert_hierarchy_handler;

		/**
		 * @var App_Expert_Response_Meta_Data
		 */
		public $app_expert_response_meta_data;

		public function __construct() {
			parent::__construct('post_tag');
			$this->rest_base = '/tags';
			$this->namespace = APP_EXPERT_API_NAMESPACE;
			$this->app_expert_hierarchy_handler = new App_Expert_Hierarchy_Handler();
			$this->app_expert_response_meta_data= new App_Expert_Response_Meta_Data($this->get_response_meta_data());
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

			register_rest_route(
				$this->namespace,
				'/' . $this->rest_base . '/(?P<id>[\d]+)',
				array(
					'args'   => array(
						'id' => array(
							'description' => __( 'Unique identifier for the term.' ),
							'type'        => 'integer',
						),
					),
					array(
						'methods'             => WP_REST_Server::READABLE,
						'callback'            => array( $this, 'get_item' ),
						'args'                => array(
							'context' => $this->get_context_param( array( 'default' => 'view' ) ),
						),
                        'permission_callback' => '__return_true',
                    ),
					'schema' => array( $this, 'get_public_item_schema' ),
				)
			);
		}

		public function get_items($request){
			$response = $this->app_expert_hierarchy_handler->get_taxonomy_children_response('post_tag' , 0 , $request , $this);
			$response = rest_ensure_response( $response );

			if (is_wp_error($response))
				return $response;

			$response = $this->app_expert_response_meta_data->attach_meta_data($response , false);

			return App_Expert_Response::wp_list_success(
				$response,
				'app_expert_tags_retrieved',
				'retrieved all tags'
			);
		}

		public function get_item($request){
			$tag = get_tag($request['id']);

			if (is_wp_error($tag))
				return $tag;

			if(!$tag instanceof WP_Term){
				return parent::get_item($request);
			}

			$response = $this->app_expert_hierarchy_handler->get_term_prepared('post_tag' , $tag , $request , array() , $this);
			$response = rest_ensure_response( $response );

			if (is_wp_error($response))
				return $response;

			$response = $this->app_expert_response_meta_data->attach_meta_data($response , true);

			return App_Expert_Response::get_api_single_success_response(
				$response,
				'app_expert_tag_retrieved',
				'retrieved requested tag'
			);
		}

		
		private function get_response_meta_data(){
			return array(
				new App_Expert_Tax_Post_Types_Meta_Data(),
			);
		}



	}
}

$app_expert_tags_controller = new App_Expert_Tags_Controller();
$app_expert_tags_controller->register_routes();
