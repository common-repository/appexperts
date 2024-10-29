<?php
class App_Expert_WC_Order
{
    public function __construct(){
        add_filter('woocommerce_rest_prepare_shop_order_object',array($this,'add_order_changes'),10,3);
        //api permission
        add_filter('woocommerce_rest_check_permissions',array($this, 'set_apis_permission'), 999, 4);
    }
    public function add_order_changes(WP_REST_Response $response,WC_Order $object, $request ){
        $response->data['billing']['country_name']  = WC()->countries->countries[ $object->get_billing_country()];
        $response->data['shipping']['country_name'] = WC()->countries->countries[ $object->get_shipping_country()];
        return $response;
    }
    public function set_apis_permission($permission, $context, $object_id, $post_type){
        if($post_type =="shop_order"&&$context=="read" && get_current_user_id()>0) return true;
        return $permission;
    }
}
new App_Expert_WC_Order();