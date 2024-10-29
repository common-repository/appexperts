<?php

class APP_EXPERT_WCB_Sync_Cart{

    private $add_item   = "/\/wc\/store\/v1\/cart\/add-item(\/)?$/";
    public function __construct(){
        add_filter( 'rest_request_before_callbacks', array($this, 'set_wcb_cart_data'), 10 , 3 );
        add_filter( 'woocommerce_add_cart_item_data', array($this, 'add_cart_item_data'), 9,4 );
        add_filter( 'woocommerce_add_to_cart_validation', array($this, 'add_to_cart_validation'), 11,3 );
        add_filter( 'woocommerce_cart_id', array($this, 'get_cart_id'), 9,5 );
        add_filter( 'rest_request_after_callbacks', array($this, 'add_cart_edits'), 99999 , 3 );
        add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 11, 2 );

    }
    private function is_valid_booking_product($product_id,$params_array){
        $product = wc_get_product( $product_id );
        return ( is_wc_booking_product( $product ) && isset($params_array['start']) && isset($params_array['end'])) ;
    }

    public function add_cart_item_data($cart_item_data, $product_id, $variation_id, $quantity){
        $current_route = $_SERVER["REQUEST_URI"];
        $current_route = explode("?",$current_route);
        $current_route = $current_route[0];
        preg_match($this->add_item,$current_route,$matches);
        if(!count($matches)) return $cart_item_data;

        if ( !$this->is_valid_booking_product($product_id,$_POST)) {
            return $cart_item_data;
        }

        $booking_local_timezone="";
        if(!isset( $_POST['booking_local_timezone']))
            $booking_local_timezone=$_POST['booking_local_timezone'];

        $_date=$_POST['start'];
        $_end_time=$_POST['end'];
        $posted=APP_EXPERT_WCB_Cost_Helper::get_posted_data($product_id,$_date,$_end_time,$quantity??1,$booking_local_timezone);

        $_POST = array_merge($_POST,$posted);
        return array_merge($cart_item_data,$posted);
    }

    public function set_wcb_cart_data($response, array $handler, \WP_REST_Request $request){

        $current_route = $request->get_route();
        preg_match($this->add_item,$current_route,$matches);
        if(!count($matches))  return $response;

        $id = $request->get_param('id');
        if ( !$this->is_valid_booking_product($id,$request->get_params())) return $response;

        $_POST=array_merge($_POST,$request->get_params());

        return $response;
    }
    public function add_to_cart_validation($is_valid,$product_id,$quantity){
        if(isset($_POST['ae_booking_id'])) return true;
        return $is_valid;
    }
    public function get_cart_id($id , $product_id, $variation_id, $variation, $cart_item_data){
        $current_route = $_SERVER["REQUEST_URI"];
        $current_route = explode("?",$current_route);
        $current_route = $current_route[0];
        preg_match($this->add_item,$current_route,$matches);
        if(!count($matches))  return $id;
        if(isset($cart_item_data["booking"])&&isset($cart_item_data["booking"]['_booking_id']))
            $_POST['ae_booking_id'] = $cart_item_data["booking"]['_booking_id'];
        return $id;
    }
    public function add_cart_edits($response, array $handler, \WP_REST_Request $request)
    {
        if (!($response instanceof WP_REST_Response)) return $response;
        $current_route  = $request->get_route();
        preg_match($this->add_item,$current_route,$matches);
        if(!count($matches))  return $response;
        if(!isset($_POST['ae_booking_id'])) return $response;
        if(count( $response->data['errors'] )) $response->data['errors'] =[];
        return $response;
    }
    public function get_item_data( $other_data, $cart_item ) {
        if ( empty( $cart_item['booking'] ) ) {
            return $other_data;
        }
        $other_data[] = array(
            'name'    => get_wc_booking_data_label( 'date', $cart_item['data'] ),
            'value'   => $cart_item['booking']['date'],
            'display' => '',
            'id'      => 'date'
        );
        $other_data[] = array(
            'name'    => get_wc_booking_data_label( 'time', $cart_item['data'] ),
            'value'   => $cart_item['booking']['time'],
            'display' => '',
            'id'      => 'time'
        );
        $other_data[] = array(
            'name'    => get_wc_booking_data_label( 'persons', $cart_item['data'] ),
            'value'   => $cart_item['booking']['_persons'][0]??"",
            'display' => '',
            'id'      => 'persons'
        );


        return $other_data;
    }
}
new APP_EXPERT_WCB_Sync_Cart();