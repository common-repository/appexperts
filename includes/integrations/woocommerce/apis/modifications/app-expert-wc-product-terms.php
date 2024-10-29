<?php
class App_Expert_WC_Product_Terms{
    private $taxonomy;
    public function __construct($taxonomy) {
        $this->taxonomy=$taxonomy;
        add_filter("woocommerce_rest_{$this->taxonomy}_query",array($this,'add_to_query'),10,2);
        add_filter( "woocommerce_rest_prepare_{$this->taxonomy}", array($this,'check_exculde_taxonomy'), 10, 3 );
        add_filter('ae_search_management_data',array($this,'search_terms'),10,7);
    }
    public function add_to_query($args, $request)
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
    public function check_exculde_taxonomy($response, $item, $request )
    {
        $excluded_ids =App_Expert_Taxonomy_Exclude_Helper::get_excluded_terms_ids($this->taxonomy);
        if(in_array($item->term_id,$excluded_ids))
        {
            return new WP_Error( 'woocommerce_rest_term_invalid', __( 'Resource does not exist.', 'woocommerce' ), array( 'status' => 404 ) );
        }
        return $response;
    }
    public function search_terms( $data,$request,$search,$limit,$page,$order,$orderby){
        if($request->get_param('wc_taxonomies')==null || !in_array($this->taxonomy,$request->get_param('wc_taxonomies')))
            return $data;
        $request = New WP_REST_Request();
        $offset = ($page - 1) * $limit;
        $request->set_param('search',$search);
        $request->set_param('page',$page);
        $request->set_param('order',$order);
        $request->set_param('orderby',$orderby);
        $request->set_param('offset',$offset);
        $request->set_param('per_page',$limit);
        $request->set_param('hide_empty',false);
        
        $obj=new App_Expert_Terms_Controller($this->taxonomy);
        $response=$obj->get_items($request);
                    
        $taxonomies=$response['data']['items'];
        $data[$this->taxonomy]=$taxonomies;
        
        return $data;
    }
   
}
new App_Expert_WC_Product_Terms('product_cat');
new App_Expert_WC_Product_Terms('product_tag');