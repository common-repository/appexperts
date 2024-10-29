<?php

class App_Expert_WCB_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes(){
        //cancel booking
        App_Expert_Route::post("wc-bookings/v1", '/booking/(?P<id>[\d]+)/cancel'   , "App_Expert_WCB_Booking_Endpoint@cancel_booking"     , $this->get_cancel_booking_parameters());
        //product slots
        App_Expert_Route::get("wc-bookings/v1" , '/products/(?P<id>[\d]+)/available-slots'   , "App_Expert_WCB_Product_Endpoint@get_available_time_slots"     , $this->get_slots_parameters());
        //product cost
        App_Expert_Route::get("wc-bookings/v1" , '/products/(?P<id>[\d]+)/cost'   , "App_Expert_WCB_Product_Endpoint@calculate_costs"     , $this->get_cost_parameters());
    }

    public function get_cancel_booking_parameters()
    {
        return array(
            'id' => array(
                'description' => __( 'booking id ', 'app-expert' ),
                'type'        => 'integer',
            ),
            'reason'          => array(
                'description' => __('reason for cancel booking', 'app-expert'),
                'type'        => 'text',
            )
        );
    }

    public function get_slots_parameters()
    {
        return  array(
            'id' => array(
                'description' => __( 'product id ', 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'date_filter'        => array(
                'description' => __( 'booking date' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'view' ),
                'required' => true
            ),
            'resource_id'        => array(
                'description' => __( 'resource id' , 'app-expert' ),
                'type'        => 'integer',
                'context'     => array( 'view' ),
            ),
            'persons'        => array(
                'description' => __( 'number of persons' , 'app-expert' ),
                'type'        => 'integer',
                'context'     => array( 'view' ),
            )
        );
    }

    private function get_cost_parameters()
    {
        return [
            'id'        => array(
                'description' => __( 'product id' , 'app-expert' ),
                'type'        => 'integer',
                'context'     => array( 'view' ),
                'required' => true
            ),
            'start_time'        => array(
                'description' => __( 'start date time' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'view' ),
                'required' => true
            ),
            'end_time'        => array(
                'description' => __( 'end date time' , 'app-expert' ),
                'type'        => 'string',
                'context'     => array( 'view' ),
                'required' => true
            ),
            'persons'        => array(
                'description' => __( 'number of persons' , 'app-expert' ),
                'type'        => 'integer',
                'context'     => array( 'view' ),
                'required' => true
            ),
        ];
    }


}

new App_Expert_WCB_Routes();

