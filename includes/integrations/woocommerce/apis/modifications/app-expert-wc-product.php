<?php
class App_Expert_WC_Product{
    private  $routesAllRegex="/\/wc\/store\/v1\/products(\/)?$/";
    private  $routesRegex="/\/wc\/store\/v1\/products\/[0-9]+$/";

    public function __construct() {
        //wc/v{x}/api
        add_filter('woocommerce_rest_prepare_product_object', array($this, 'add_products_edits'), 20, 3);
        add_filter('woocommerce_rest_product_object_query',array($this,'add_to_query'),10,2);
        add_filter( "rest_product_collection_params", array($this,'add_additional_params_to_product_api'), 10, 1 );
        //wc/store/v1/products (Not used)
        //todo:remove from here
        add_filter('rest_request_after_callbacks', array($this, 'modify_product_data'), 999, 3);
        //api permission
        add_filter('woocommerce_rest_check_permissions',array($this, 'set_apis_permission'), 999, 4);
        add_filter('ae_search_management_data',array($this,'search_products'),10,7);
    }
    public function add_to_query($args, $request)
    {
        $additional_query=[];
		// Set tax_query to filter products with tax have exclude mobile flag
		foreach ( App_Expert_Taxonomy_Exclude_Helper::$product_taxonomies as $taxonomy ) {
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
        if (!empty($request['appexpert_attributes']) && is_array($request['appexpert_attributes'])) {

            $additional_tax_query = [];
            $all_available_attr_names = wc_get_attribute_taxonomy_names();
            foreach ($request['appexpert_attributes'] as $attribute_name => $attribute_term_ids) {
                if (in_array($attribute_name, $all_available_attr_names, true)) {
                    $additional_tax_query[] = array(
                        'taxonomy' => $attribute_name,
                        'field'    => 'term_id',
                        'terms'    => explode(",", $attribute_term_ids),
                    );
                }
            }
            $args['tax_query'] = array_merge($additional_tax_query, $args['tax_query']); // WPCS: slow query ok.
        }

        if(isset($_GET['orderby'])){
            if ('rating' == $_GET['orderby']) {
                if(isset($_GET['order'])){
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = $_GET['order'];
                    $args['meta_key'] = '_wc_average_rating';
                }else{
                    $args['orderby'] = 'meta_value_num';
                    $args['order'] = 'DESC';
                    $args['meta_key'] = '_wc_average_rating';
                }
            }
        }

        return $args;
    }
    public function add_products_edits($response, $product,$request)
    {
        if (empty($response->data))
            return $response;

        if($request->has_param('id'))
        { 
            $check=$this->check_exclude_tax_on_single_product($response->data);
            if (is_wp_error($check))
            {
                return $check;
            }
        }
        $response->data= App_Expert_WC_Product_Helper::get_data($response->data,$product);
        return $response;
    }
    public function add_additional_params_to_product_api ($params){
        $params['appexpert_attributes'] = array(
            'description' => __( '(Array) filter with multiple attribute. example:appexpert_attributes[pa_color]=20,30' ),
            'type'        => 'Array',
        );
        return $params;
    }


    public function modify_product_data($response, array $handler, \WP_REST_Request $request) {
        if( $response instanceof WP_Error) return $response;
        $current_route = $request->get_route();
        $matches=[];
        //list of products
        preg_match($this->routesAllRegex,$current_route,$matches);
        if(count($matches)){
            return $this->edit_list($response);
        }
        preg_match($this->routesRegex,$current_route,$matches);
        if(count($matches)){
            return $this->edit_one($response);
        }
        return $response;
    }
    private function edit_list(WP_REST_Response $response)
    {
        $new_data=[];
        foreach ($response->get_data() as $product){
            $new_data[]= App_Expert_WC_Product_Helper::get_data($product);
        }
        $response->set_data($new_data);
        return $response;
    }
    private function edit_one(WP_REST_Response $response)
    {
        $response->set_data(App_Expert_WC_Product_Helper::get_data($response->get_data()));
        return $response;
    }

    public function set_apis_permission($permission, $context, $object_id, $post_type){
        if($post_type =="product"&&$context=="read" && get_current_user_id()>0 ) return true;
        return $permission;
    }
    // check if single product have excluded flag tax
    public function check_exclude_tax_on_single_product($data){
        foreach ( App_Expert_Taxonomy_Exclude_Helper::$product_taxonomies as $taxonomy ) {
            $terms_ids =App_Expert_Taxonomy_Exclude_Helper::get_excluded_terms_ids($taxonomy);
            if(!empty($terms_ids) && has_term($terms_ids ,$taxonomy,$data['id']))
            {
                return new WP_Error( 'woocommerce_rest_product_invalid_id', __( 'Invalid product ID.', 'woocommerce' ), array( 'status' => 404 ) );
            }
           
		}
        return null;
    }

    public function search_products($data,$request,$search,$limit,$page,$order,$orderby){
        if($request->get_param('wc_types')==null || !in_array('product',$request->get_param('wc_types')))
            return $data;

        $obj= new WC_REST_Products_Controller();
        $get_req= new WP_REST_Request();
        $get_req->set_param('search',$search);
        $get_req->set_param('page',$page);
        $get_req->set_param('per_page',$limit);
        if($order) $get_req->set_param('order',$order);
        if($orderby) $get_req->set_param('orderby',$orderby);
        $response=$obj->get_items($get_req);
        $data['products']=$response->get_data();
        return $data;
    }
}
new App_Expert_WC_Product();