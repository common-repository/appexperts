<?php
class APP_EXPERT_WCB_Product {

    public function __construct()
    {
        add_filter( 'ae_rest_product_obj', array( $this, 'add_product_edits' ),20,2);
    }

    function add_product_edits($response_product,$_wc_prod){
        if($response_product['type'] == 'booking'){
           // $_wc_prod = new WC_Product_Booking($_wc_prod->get_id());
            $duration      = $_wc_prod->get_duration();
            $response_product['duration']      = $duration;
            $response_product['duration_type'] = $_wc_prod->get_duration_type();
            $duration_unit = $_wc_prod->get_duration_unit();
            $response_product['duration_unit'] =$duration_unit;
            if ( 'minute' === $duration_unit ) {
                $duration_unit = _n( 'minute', 'minutes', $duration, 'woocommerce-bookings' );
            } elseif ( 'hour' === $duration_unit ) {
                $duration_unit = _n( 'hour', 'hours', $duration, 'woocommerce-bookings' );
            } elseif ( 'day' === $duration_unit ) {
                $duration_unit = _n( 'day', 'days', $duration, 'woocommerce-bookings' );
            } elseif ( 'month' === $duration_unit ) {
                $duration_unit = _n( 'month', 'months', $duration, 'woocommerce-bookings' );
            } else {
                $duration_unit = _n( 'block', 'blocks', $duration, 'woocommerce-bookings' );
            }
            $response_product['duration_unit_label'] =$duration_unit;
            $response_product['persons']['has_persons']=$_wc_prod->has_persons();
            if($_wc_prod->has_persons()){
                $response_product['persons']['min']=$_wc_prod->get_min_persons();
                $response_product['persons']['max']=$_wc_prod->get_max_persons();
                $response_product['persons']['has_person_cost_multiplier']=$_wc_prod->get_has_person_cost_multiplier();
                $response_product['persons']['has_person_qty_multiplier']=$_wc_prod->get_has_person_qty_multiplier();
            }

            $context = 'view';
            $response_product =array_merge($response_product, array(
                'apply_adjacent_buffer'      => $_wc_prod->get_apply_adjacent_buffer( $context ),
                'availability'               => $_wc_prod->get_availability( $context ),
                'block_cost'                 => $_wc_prod->get_block_cost( $context ),
                'buffer_period'              => $_wc_prod->get_buffer_period( $context ),
                'calendar_display_mode'      => $_wc_prod->get_calendar_display_mode( $context ),
                'cancel_limit_unit'          => $_wc_prod->get_cancel_limit_unit( $context ),
                'cancel_limit'               => $_wc_prod->get_cancel_limit( $context ),
                'check_start_block_only'     => $_wc_prod->get_check_start_block_only( $context ),
                'cost'                       => $_wc_prod->get_cost( $context ),
                'default_date_availability'  => $_wc_prod->get_default_date_availability( $context ),
                'display_cost'               => $_wc_prod->get_display_cost( $context ),
                'duration_type'              => $_wc_prod->get_duration_type( $context ),
                'duration_unit'              => $_wc_prod->get_duration_unit( $context ),
                'duration'                   => $_wc_prod->get_duration( $context ),
                'enable_range_picker'        => $_wc_prod->get_enable_range_picker( $context ),
                'first_block_time'           => $_wc_prod->get_first_block_time( $context ),
                'has_person_cost_multiplier' => $_wc_prod->get_has_person_cost_multiplier( $context ),
                'has_person_qty_multiplier'  => $_wc_prod->get_has_person_qty_multiplier( $context ),
                'has_person_types'           => $_wc_prod->get_has_person_types( $context ),
                'has_persons'                => $_wc_prod->get_has_persons( $context ),
                'has_resources'              => $_wc_prod->get_has_resources( $context ),
                'has_restricted_days'        => $_wc_prod->get_has_restricted_days( $context ),
                'max_date'                   => $_wc_prod->get_max_date(),
                'max_date_value'             => $_wc_prod->get_max_date_value( $context ),
                'max_date_unit'              => $_wc_prod->get_max_date_unit( $context ),
                'max_duration'               => $_wc_prod->get_max_duration( $context ),
                'max_persons'                => $_wc_prod->get_max_persons( $context ),
                'min_date'                   => $_wc_prod->get_min_date(),
                'min_date_value'             => $_wc_prod->get_min_date_value( $context ),
                'min_date_unit'              => $_wc_prod->get_min_date_unit( $context ),
                'min_duration'               => $_wc_prod->get_min_duration( $context ),
                'min_persons'                => $_wc_prod->get_min_persons( $context ),
                'person_types'               => $_wc_prod->get_person_types( $context ),
                'pricing'                    => $_wc_prod->get_pricing( $context ),
                'qty'                        => $_wc_prod->get_qty( $context ),
                'requires_confirmation'      => $_wc_prod->requires_confirmation(),
                'resource_label'             => $_wc_prod->get_resource_label( $context ),
                'resource_base_costs'        => $_wc_prod->get_resource_base_costs( $context ),
                'resource_block_costs'       => $_wc_prod->get_resource_block_costs( $context ),
                'resource_ids'               => $_wc_prod->get_resource_ids( $context ),
                'resources_assignment'       => $_wc_prod->get_resources_assignment( $context ),
                'restricted_days'            => $_wc_prod->get_restricted_days( $context ),
                'can_be_cancelled'           => $_wc_prod->can_be_cancelled(),
                'user_can_cancel'            => $_wc_prod->get_user_can_cancel( $context ),
            ));
        }
        return $response_product;
    }
    
}
new APP_EXPERT_WCB_Product();
