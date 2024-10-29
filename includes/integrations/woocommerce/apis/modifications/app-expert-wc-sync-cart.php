<?php
class App_Expert_WC_Sync_cart{
    private $sync_used_routes =[
        //route       => regex for it
        'add-item'    => "/\/wc\/store\/v1\/cart\/add-item(\/)?$/",
        'update-item' => "/\/wc\/store\/v1\/cart\/update-item(\/)?$/",
        'delete-item' => "/\/wc\/store\/v1\/cart\/remove-item(\/)?$/",
        'list-items'  => "/\/wc\/store\/v1\/cart(\/)?$/",
    ];
    public function __construct() {
        add_filter( 'rest_request_before_callbacks', array($this, 'set_cart_hash'), 1 , 3 );
        add_filter( 'rest_request_after_callbacks', array($this, 'add_cart_edits'), 99999 , 3 );
        add_filter( 'woocommerce_store_api_disable_nonce_check', array($this, 'disable_nonce_check'), 10 );
    }
    public function disable_nonce_check($check){
        //todo: find cycle so we can choose to enable/disable it
        return true;
    }
    public function set_cart_hash( $response, $handler, WP_REST_Request $request ) {
        //global $woocommerce;
        //return error
        //init new session
        //wp_woocommerce_session_72524b994cad88bbd8aa6dae98aebbfd=t_c42eee87114ad0f53af3cffa1ebbc2%7C%7C1677605960%7C%7C1677605961%7C%7C02de505c2cb3e186d084fac29591297d
        
        get_current_user_id();
        return $response;
    }
    public function add_cart_edits($response, array $handler, \WP_REST_Request $request){
        if(!($response instanceof WP_REST_Response)) return $response;
        $add_flag = false;
        $matches=[];
        $current_route = $request->get_route();
        foreach ($this->sync_used_routes as $regex){
            preg_match($regex,$current_route,$matches);
            if(count($matches)){
                $add_flag =true;
                break;
            }
        }
        if(!$add_flag) return $response;
        if(!isset( $response->data['items'] )) return $response;

        if(count( $response->data['items'] )){
            foreach ($response->data['items'] as $i=>$item){
                $response->data['items'][$i] =  App_Expert_WC_Product_Helper::get_data($item,null,'cart');
                if(isset( $response->data['shipping_rates'])&&isset(  $response->data['shipping_rates'][0])&&isset(  $response->data['shipping_rates'][0]['items'])&&isset($response->data['shipping_rates'][0]['items'][$i]))
                    $response->data['items'][$i]['key'] =  $response->data['shipping_rates'][0]['items'][$i]['key'];
            }
        }
        $response->data["totals"]->ae_total_items = (float) WC()->cart->get_subtotal();

        return $response;
    }
}
new App_Expert_WC_Sync_cart();