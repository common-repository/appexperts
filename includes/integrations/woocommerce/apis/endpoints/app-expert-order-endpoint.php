<?php

class App_Expert_Order_Endpoint
{
    public function cancel_order($request) {
        $order_id = $request['id'];
        $user_id = get_current_user_id();
        $order = wc_get_order( $order_id );
        $order_can_cancel = $order->has_status( apply_filters( 'woocommerce_valid_order_statuses_for_cancel', array( 'pending', 'failed' ), $order ) );

        // check if order with this id exists
        if($order){
            // check if user can cancel order
            if($order->get_customer_id() == $user_id){
                // check if order can be cancelled
                if($order_can_cancel){
                    $order->update_status( 'cancelled', __( 'Order cancelled by customer.', 'woocommerce' ) );
                    return App_Expert_Response::success( 'app_expert_success', __('Order was cancelled successfully', 'app-expert'),[]);
                }else{
                    //todo: use App_Expert_Response
                    return new WP_Error(
                        'app_expert_not_acceptable',
                        __('Order has invalid status for cancel', 'app-expert'),
                        array(
                            'status' => 406
                        )
                    );
                }
            }else{
                //todo: use App_Expert_Response
                return new WP_Error(
                    'app_expert_forbidden_action',
                    __('You are not authorized to cancel this order', 'app-expert'),
                    array(
                        'status' => 403
                    )
                );
            }
        }else{
            return new WP_Error(
                'app_expert_not_found',
                __('No order found', 'app-expert'),
                array(
                    'status' => 404
                )
            );
        }
    }

}