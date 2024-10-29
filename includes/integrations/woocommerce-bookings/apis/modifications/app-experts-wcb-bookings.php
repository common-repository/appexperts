<?php
class APP_EXPERTS_WCB_Bookings{

    public function __construct()
    {
        add_filter('woocommerce_rest_wc_booking_object_query', array($this,'booking_object_querys'), 10, 2);
        add_filter( 'woocommerce_rest_prepare_wc_booking_object', array( $this, 'add_wc_booking_edits' ),20,3);
        add_filter( "rest_wc_booking_collection_params",array($this,'add_filters_param'));
        add_filter('woocommerce_rest_check_permissions',array($this, 'set_apis_permission'), 999, 4);

    }

    function add_filters_param($query_params) {

        $query_params['start_after'] = array(
			'description' => __( 'Limit response to bookings start after or equal a given ISO8601 compliant date.' ),
			'type'        => 'string',
			'format'      => 'date',
		);

        $query_params['end_before'] = array(
			'description' => __( 'Limit response to bookings ended before a given ISO8601 compliant date.' ),
			'type'        => 'string',
			'format'      => 'date',
		);
        
        return $query_params;	
    }

    public function booking_object_querys ($args, $request)
    {
        $user_id = get_current_user_id();

        if(isset( $user_id)){
            $customer_meta_query =  array(
                'key' => '_booking_customer_id',
                'value' => $user_id ,
                'compare' => '=',
            );

            if ( isset( $args['meta_query'] ) ) {
                $args['meta_query']['relation'] = 'AND';
                $args['meta_query'][] = $customer_meta_query;
            } else {
                $args['meta_query'] = array();
                $args['meta_query'][] = $customer_meta_query;
            }
        }

        if(isset($request['start_after'])){
            $start_date_meta_query = array(
                'key' => '_booking_start',
                'value' =>  date( 'Ymd', strtotime($request['start_after'])),
                'compare' => '>=',
                'type' => 'DATETIME'
            );

            if ( isset( $args['meta_query'] ) ) {
                $args['meta_query']['relation'] = 'AND';
                $args['meta_query'][] = $start_date_meta_query;
            } else {
                $args['meta_query'] = array();
                $args['meta_query'][] = $start_date_meta_query;
            }     
        }

        if(isset($request['end_before'])){
            $start_date_meta_query = array(
                'key' => '_booking_end',
                'value' =>  date( 'Ymd', strtotime($request['end_before'])),
                'compare' => '<',
                'type' => 'DATETIME'
            );

            if ( isset( $args['meta_query'] ) ) {
                $args['meta_query']['relation'] = 'AND';
                $args['meta_query'][] = $start_date_meta_query;
            } else {
                $args['meta_query'] = array();
                $args['meta_query'][] = $start_date_meta_query;
            }     
        }
        
        return $args;
    }
    
    public function add_wc_booking_edits($response,WC_Booking $booking)
    {
        if (empty($response->data))
            return $response;
        $prod = $booking->get_product();
        if(!$prod){
           $other_booking=get_post_meta($booking->get_id(),"_booking_duplicate_of",true);
           if(!empty($other_booking)){
               $booking_obj=new WC_Booking($other_booking);
               $prod = $booking_obj->get_product();
           }
        }

        $response->data['service_name'] = $prod?$prod->get_name():"";

       if($prod)
       {
            $attachment_id = $prod->get_image_id();
            $attachment = wp_get_attachment_image_src($attachment_id, 'full');
            if (!is_array($attachment)) {
                $attachment = [""];
            }
           $response->data['service_image'] = $attachment[0];
           $terms = get_the_terms( $prod->get_id(), 'product_cat' );
           $response->data['service_cat'] = $terms;
           $response->data['user_can_cancel_service'] = get_post_meta($prod->get_id(),'_wc_booking_user_can_cancel', true)?get_post_meta($prod->get_id(),'_wc_booking_user_can_cancel', true):0;
           $response->data['service_cancel_limit'] = get_post_meta($prod->get_id(),'_wc_booking_cancel_limit', true)?get_post_meta($prod->get_id(),'_wc_booking_cancel_limit', true):null;
           $response->data['service_cancel_limit_unit'] = get_post_meta($prod->get_id(),'_wc_booking_cancel_limit_unit', true)?get_post_meta($prod->get_id(),'_wc_booking_cancel_limit_unit', true):null;
           $response->data['order_payment_method'] = $booking->get_order()?$booking->get_order()->get_payment_method_title():null;
           $response->data['order_status'] =  $booking->get_order()?$booking->get_order()->get_status():null;
           $response->data['is_rated'] = get_post_meta($booking->get_id(),'_is_rated', true)?get_post_meta($booking->get_id(),'_is_rated', true):0;
           $response->data['person_counts'] = $booking->get_persons();
       }else {
           $response->data['service_image']                 = "";
           $response->data['service_cat']                   = [];
           $response->data['user_can_cancel_service']       = 0;
           $response->data['service_cancel_limit']          = null;
           $response->data['service_cancel_limit_unit']     = null;
           $response->data['order_payment_method']          = null;
           $response->data['order_status']                  = null;
           $response->data['is_rated']                      = 0;
           $response->data['person_counts']                 = null;
       }
        return $response;
    }

    public function set_apis_permission($permission, $context, $object_id, $post_type){
        if($post_type =="wc_booking"&&$context=="read" && get_current_user_id()>0) return true;
        return $permission;
    }
}
new APP_EXPERTS_WCB_Bookings();
