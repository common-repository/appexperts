<?php
class App_Expert_WC_Product_reviews{
    public function __construct() {
        add_filter( 'woocommerce_rest_prepare_product_review', array($this,"add_extra_params"),10,3);
    }
    public function add_extra_params(WP_REST_Response $response, $review, $request ){
       $product_id =  $response->data["product_id"];
       $wc_product=wc_get_product($product_id);
       if($product_id){
           $response->data['review_count']=$wc_product->get_review_count();
           $response->data['avg_rate']=$wc_product->get_average_rating();
       }
       return $response;
    }
}
new App_Expert_WC_Product_reviews();