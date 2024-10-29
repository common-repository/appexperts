<?php
class App_Expert_Taxonomies_types_Controller extends WP_REST_Taxonomies_Controller {

    public function __construct() {
        $this->rest_base = '/taxonomies';
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
    }

    public function get_items($request){
        $args='';

        if(!empty($request->get_param('show_in_rest')))
        {
            $args = array(
                'public'   => true,
              //  '_builtin' => false,
                'show_in_rest' => true
            );
        }
        $registered = $this->get_collection_params();

        if ( isset( $registered['type'] ) && ! empty( $request['type'] ) ) {
            $taxonomies = get_object_taxonomies( $request['type'], 'objects' );
        } else {
            $taxonomies = get_taxonomies( $args, 'objects' );
        }

        $data = array();

        foreach ( $taxonomies as $tax_type => $value ) {
            if ( empty( $value->show_in_rest ) || ( 'edit' === $request['context'] && ! current_user_can( $value->cap->assign_terms ) ) ) {
                continue;
            }

            $tax               = $this->prepare_item_for_response( $value, $request );
            $tax               = $this->prepare_response_for_collection( $tax );
            $data[ $tax_type ] = $tax;
            if ( class_exists( 'WooCommerce' ) ) {
                try{
                    $woocommerce_cat_tax = get_taxonomies(['name' => 'product_cat'], 'objects' );
                    $woocommerce_cat_tax['product_cat']->rest_base = 'products/categories';
                    $woocommerce_tag_tax = get_taxonomies(['name' => 'product_tag'], 'objects' );
                    $woocommerce_tag_tax['product_tag']->rest_base = 'products/tags';
                    $woocommerce_taxonomies = array_merge($woocommerce_cat_tax, $woocommerce_tag_tax);
                    foreach ( $woocommerce_taxonomies as $tax_type => $value ) {
                        $tax               = $this->prepare_item_for_response( $value, $request );
                        $tax               = $this->prepare_response_for_collection( $tax );
                        $taxonomies[ $tax_type ] = $tax;
                    }
                }catch(\Exception $e){
                    error_log('An error when trying to get woocommerce taxonomies');
                }
            }
        }

        if ( empty( $data ) ) {
            // Response should still be returned as a JSON object when it is empty.
            $data = (object) $data;
        }

        $response= new WP_REST_Response();
        $response->set_data($data);

        return App_Expert_Response::wp_list_success(
            $response,
            'app_expert_taxonomy_types_retrieved',
            'retrieved all taxonomy Types'
        );

    }
    public function prepare_item_for_response( $item, $request ){
       $res = parent::prepare_item_for_response($item,$request);
       return apply_filters("ae_post_search_response",$res,$request);
    }
    public function get_collection_params() {
        $parent=parent::get_collection_params();
        $parent['show_in_rest']=array(
            'description' => __( 'Limit results to taxonomies that have rest end points.' ),
            'type'        => 'int',
        );
        return $parent;
    }

}

$app_expert_taxonomies_controller = new App_Expert_Taxonomies_types_Controller();
$app_expert_taxonomies_controller->register_routes();