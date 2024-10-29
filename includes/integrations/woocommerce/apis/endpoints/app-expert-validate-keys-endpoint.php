<?php

Class App_Expert_Validate_Keys_Endpoint{
    //Copied from => WC_REST_Authentication
    public function validate(WP_REST_Request $request){
        $this->auth_method = 'basic_auth';

        $consumer_key    = $request->get_param('consumer_key'); // WPCS: CSRF ok, sanitization ok.
        $consumer_secret = $request->get_param('consumer_secret'); // WPCS: CSRF ok, sanitization ok.

        // Get user data.
        $user = $this->get_user_data_by_consumer_key( $consumer_key );
        if ( empty( $user ) || ! hash_equals( $user->consumer_secret, $consumer_secret ) ) { // @codingStandardsIgnoreLine
           return App_Expert_Response::fail("wc_error_authenticating_keys",__("invalid keys",'app-expert'),[],401);
        }
        return App_Expert_Response::success("wc_success_authenticating_keys",__("valid keys",'app-expert'),[]);

    }
    private function get_user_data_by_consumer_key( $consumer_key ) {
        global $wpdb;

        $consumer_key = wc_api_hash( sanitize_text_field( $consumer_key ) );
        $user         = $wpdb->get_row(
            $wpdb->prepare(
                "
			SELECT key_id, user_id, permissions, consumer_key, consumer_secret, nonces
			FROM {$wpdb->prefix}woocommerce_api_keys
			WHERE consumer_key = %s
		",
                $consumer_key
            )
        );

        return $user;
    }
}