<?php
class APP_EXPERT_WCB_Cost_Helper{
    public static function get_posted_data($id,$_date,$_end_time,$qty=1,$local_timezone=""){
        $_start_timestamp  = strtotime($_date);
        $_start_date_month = date("m", $_start_timestamp);
        $_start_date_day   = date("d", $_start_timestamp);
        $_start_date_year  = date("Y", $_start_timestamp);
        $dateDifference = date_diff(date_create($_date), date_create($_end_time));
        $product           = wc_get_product( $id );
        $duration          = ($dateDifference->h)/$product->get_duration();
        return [
//            'add-to-cart'=>$id,
            'wc_bookings_field_persons'=>$qty,
            'wc_bookings_field_start_date_month'=>$_start_date_month,
            'wc_bookings_field_start_date_day'=>$_start_date_day,
            'wc_bookings_field_start_date_year'=>$_start_date_year,
            'start_time'=>$_date,
            'wc_bookings_field_start_date_time'=>$_date,
            'end_time'=>$duration,
            'wc_bookings_field_duration'=>$duration,
            'wc_bookings_field_start_date_local_timezone'=>$local_timezone,
        ];

    }


    public static function calculate_cost($product_id,$_date,$_end_time,$qty=1)
    {
        $product = wc_get_product( $product_id );
        $posted = self::get_posted_data($product_id,$_date,$_end_time,$qty);
        if ( ! $product ) {
            wp_send_json( array(
                'result' => 'ERROR',
                'message'   =>  __( 'This booking is unavailable.', 'woocommerce-bookings' ) ,
            ) );
        }

        $booking_data = wc_bookings_get_posted_data( $posted, $product );
        $cost = \WC_Bookings_Cost_Calculation::calculate_booking_cost( $booking_data, $product );

        if ( is_wp_error( $cost ) ) return $cost;


        if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
            if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                $display_price = wc_get_price_including_tax( $product, array( 'price' => $cost ) );
            } else {
                $display_price = $product->get_price_including_tax( 1, $cost );
            }
        } else {
            if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                $display_price = wc_get_price_excluding_tax( $product, array( 'price' => $cost ) );
            } else {
                $display_price = $product->get_price_excluding_tax( 1, $cost );
            }
        }

        if ( version_compare(WC_BOOKINGS_VERSION, '2.4.0', '>=' ) ) {
            $price_suffix = $product->get_price_suffix( $cost, 1 );
        } else {
            $price_suffix = $product->get_price_suffix();
        }

        $cost = array(
            'display_price' => $display_price,
            'price_suffix' => $price_suffix
        );
        return $cost;
    }


}