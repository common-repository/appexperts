<?php
class App_Expert_WCB_Product_Endpoint extends WP_REST_Controller{

    public function get_available_time_slots ($request)
    {
        $product = get_wc_product_booking( wc_get_product( $request['id'] ) );
        $from = $request['date_filter'];


        $min=$product->get_min_persons();
        $persons =(int)($request['persons']?:$min);

        $timezone         = wp_timezone();

        $date = new DateTime("now", $timezone );
        $today = $date->format('Y-m-d');

        if($from == $today){
            $from = date('Y-m-d H:00',strtotime($date->format('Y-m-d H:i:s') . "+1 hour"));
        }

        $to = date('Y-m-d H:i:s',strtotime($request['date_filter'] . "23:59:59"));

        $blocks = $product->get_blocks_in_range( strtotime($from), strtotime($to),array(),$request['resource_id']??0,array(),false);
        $slots = wc_bookings_get_time_slots($product, $blocks, array(),  $request['resource_id']??0, strtotime($from), strtotime($to), false);

        $available_slots = [];
        foreach ($slots as $key => $slot) {
            $_slot = [];
            $timezone         = wp_timezone();
            $_slot['start_time'] = $this->get_time(date($key), $timezone);
            $duration_unit = $product->get_duration_unit();
            $duration = $product->get_duration();
            $_slot['end_time'] = $this->get_time(date(strtotime($_slot['start_time'] . "+".$duration .$duration_unit."")), $timezone);
            $_slot['resources'] = $slot['resources'];
            $_slot['cost'] = APP_EXPERT_WCB_Cost_Helper::calculate_cost($request['id'],$_slot['start_time'], $_slot['end_time'], $persons);
            if(!($_slot['cost'] instanceof WP_Error))
                $available_slots[] = $_slot;
        }

        return $available_slots;

    }

    public function get_time( $timestamp, $timezone ) {
        $server_time = new DateTime( date( 'Y-m-d\TH:i:s', $timestamp ), $timezone );
        return $server_time->format( "Y-m-d\TH:i" );
    }

    public function calculate_costs(WP_REST_Request $request) {
        $booking_id = $request->get_param('id');
        $product    = wc_get_product( $booking_id );
        if ( ! $product ) {
            wp_send_json( array(
                'result' => 'ERROR',
                'message'   =>  __( 'This booking is unavailable.', 'woocommerce-bookings' ) ,
            ) );
        }
        $_date             = $request->get_param('start_time');
        $_end_time         = $request->get_param('end_time');
        $persons           = $request->get_param('persons');
        $cost=APP_EXPERT_WCB_Cost_Helper::calculate_cost($booking_id,$_date,$_end_time,$persons);


        // Send the output
        return App_Expert_Response::success(
            "cost_is_calculated",
            "cost_is_calculated",
            $cost);
    }



}