<?php
class App_Expert_WCB_Booking_Endpoint extends WP_REST_Controller{

    public function cancel_booking ($request)
    {
        $booking_id         = absint( $request['id'] );
        //fix delete issue
        $duplication        = get_post_meta($booking_id,'_booking_duplicate_of',true);
        if(!empty($duplication)){
            $booking_id         = absint( $duplication );
        }
        $booking            = get_wc_booking( $booking_id );
        if(!$booking){
            return new WP_Error(
                'app_expert_wc_booking_not_valid',
                __( 'Invalid booking.', 'app-expert' ),
                array(
                    'status' => 404,
                )
            );
        }
        if($booking->get_customer_id()!= get_current_user_id()){
            return new WP_Error(
                'app_expert_forbidden_action',
                __('You are not authorized to cancel this booking', 'app-expert'),
                array(
                    'status' => 403
                )
            );
        }
        $booking_can_cancel = $booking->has_status( get_wc_booking_statuses( 'cancel' ) );
        if ( ! $booking_can_cancel ) {
            return new WP_Error(
                'app_expert_wc_booking_can_not_cancel',
                __( 'Your booking can no longer be cancelled.', 'app-expert' ),
                array(
                    'status' => 403,
                )
            );
        }
        if ( $booking_can_cancel ) {
            // Cancel the booking
            try{
                ob_start();
                update_post_meta($booking_id,"customer_cancel_booking",1);
                $booking->update_status( 'cancelled' );
                if(isset($request['reason'])){
                    update_post_meta($booking_id, 'cancel_reason', $request['reason']);
                }

                WC_Cache_Helper::get_transient_version( 'bookings', true );

                do_action( 'woocommerce_bookings_cancelled_booking', $booking->get_id() );
                ob_get_clean();
            }catch (Exception $e){
                return new WP_Error(
                    'app_expert_wc_booking_can_not_cancel',
                    $e->getMessage(),
                    array(
                        'status' => 500,
                    )
                );
            }

            return App_Expert_Response::success('booking_cancelled','Booking is cancelled successfully!',[]);

        }
        return new WP_Error(
            'app_expert_wc_booking_can_not_cancel',
            __( 'Your booking can no longer be cancelled.', 'app-expert' ),
            array(
                'status' => 403,
            )
        );
    }
    
}