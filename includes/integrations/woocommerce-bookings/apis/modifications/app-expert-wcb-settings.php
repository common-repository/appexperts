<?php
class APP_EXPERT_WCB_Timezone {

    public function __construct()
    {
        add_filter( 'ae_settings_data', array( $this, 'add_booking_timezone_settings' ));
    }

    function add_booking_timezone_settings($data){


        $data['booking'] =  [
            'use_server_timezone_for_actions' => WC_Bookings_Timezone_Settings::get( 'use_server_timezone_for_actions' ),
            'use_client_timezone'             => WC_Bookings_Timezone_Settings::get( 'display_timezone' ),
            'display_timezone'                => WC_Bookings_Timezone_Settings::get( 'display_timezone' ),
            'use_client_firstday'             => WC_Bookings_Timezone_Settings::get( 'use_client_firstday' ),
        ];
        $data['server_timezone'] =  ['time_zone' => wc_booking_get_timezone_string()];

        return $data;
    }
    
}
new APP_EXPERT_WCB_Timezone();
