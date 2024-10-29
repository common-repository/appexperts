<?php
if (!defined('ABSPATH')){
	exit();
}

if (!class_exists('App_Expert_Posts_Controller')){

	class App_Expert_Posts_Controller extends WP_REST_Posts_Controller {


		/**
		 * @var App_Expert_Response_Meta_Data
		 */
		private $app_expert_response_meta_data;

		public function __construct() {
			parent::__construct('post');
			$this->rest_base = '/posts';
			$this->namespace = APP_EXPERT_API_NAMESPACE;
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
						'permission_callback' => array( $this, 'get_items_permissions_check' ),
						'args'                => $this->get_collection_params(),
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
						'permission_callback' => array( $this, 'get_item_permissions_check' ),
						'args'                => $get_item_args,
					),
					'schema' => array( $this, 'get_public_item_schema' ),
				)
			);
		}



		public function get_items($request){
			$response =  parent::get_items($request);
			if (is_wp_error($response))
				return $response;

			$response = $this->app_expert_response_meta_data->attach_meta_data($response , false);
			return App_Expert_Response::wp_list_success(
				$response,
				'app_expert_posts_retrieved',
				'retrieved all posts'
			);
		}

		public function get_item($request){
			$response =  parent::get_item($request);
			if (is_wp_error($response))
				return $response;

			$response = $this->app_expert_response_meta_data->attach_meta_data($response , true);
			
			return App_Expert_Response::get_api_single_success_response(
				$response,
				'app_expert_post_retrieved',
				'retrieved requested post'
			);
		}




		/**
		 * @return array
		 */
		private function get_response_meta_data(){
			return array(
				new App_Expert_Post_Translation_Meta_Data(),
				new App_Expert_Image_Meta_Data(),
				new App_Expert_Tag_Meta_Data(),
				new App_Expert_Category_Meta_Data(),
				new App_Expert_Author_Meta_Data(),
				new App_Expert_Supports_Gallery_Meta_Data(),
				new App_Expert_Gallery_Videos_Meta_Data()
			);
		}



	}
}

$app_expert_posts_controller = new App_Expert_Posts_Controller();
$app_expert_posts_controller->register_routes();
