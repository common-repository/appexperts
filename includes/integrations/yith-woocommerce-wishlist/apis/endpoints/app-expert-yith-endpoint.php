<?php

class App_Expert_Yith_Endpoint extends WP_REST_Controller
{
    public function get_wishlists(WP_REST_Request $request) 
    {
        $user_id = get_current_user_id();
        try {
            $wishlists = \WC_Data_Store::load( 'wishlist' )->query( [ 'user_id' => $user_id] );
        } catch( \Exception $e ){
            // return error response
            return new WP_Error(
                'wishlist_is_not_valid',
                $e->getMessage(),
                array(
                    'status' => 500,
                )
            );
        }

        $user_wishlists = [];
        if(!empty($wishlists)){
            foreach( $wishlists as $wishlist ) {
                $user_wishlists[] = $wishlist->get_data();
            }
        }

        $response['code'] = 'app_expert_retrieved_wishlists';
        $response['message'] = 'Wishlists retrieved successfully.';
        $response['data'] = [
            'status' => 200,
            'wishlists' => $user_wishlists
        ];
        return $response;

    }

    public function get_wishlist_products(WP_REST_Request $request) {

        $user_id = get_current_user_id();
        $wishlists = \WC_Data_Store::load( 'wishlist' )->query( [ 'user_id' => $user_id] );
        if( empty( $wishlists ) ) {
            return new WP_Error(
                'app_expert_retrieved_wishlist_products',
                __('Wishlist products retrieved successfully.', 'app-expert'),
                array(
                    'status' => 200,
                    'items' => []
                )
            );
        }else{
            $wishlist_id = $wishlists[0]->get_id();
        }
        $limit = $request->get_param( 'per_page' );
        $offset = ($request->get_param( 'page' ) - 1) * $limit;
        
        if(!$wishlist_id || $wishlist_id == 0){
            $wishlist_option = "all";
        }else{
            $wishlist_option = $wishlist_id;
        }
        
        try {
            $wishlist_products = \WC_Data_Store::load( 'wishlist-item' )->query( [ 
                                                                'user_id' => $user_id,
                                                                'wishlist_id' => $wishlist_option,
                                                                'limit' => $limit,
                                                                'offset' => $offset
                                                                ] );
        } catch( \Exception $e ){
            // return error response
            return new WP_Error(
                'wishlist_products_is_not_valid',
                $e->getMessage(),
                array(
                    'status' => 500,
                )
            );
        }
        $products = [];
        if( !empty( $wishlist_products ) ) {
            $api=new WC_REST_Products_Controller();
            foreach( $wishlist_products as $wishlist_product ) {
                $product_id = $wishlist_product->get_product_id();
                $prod = wc_get_product($product_id);
                $data = $api->prepare_object_for_response($prod, $request);
                $products[] = $this->prepare_response_for_collection( $data );
            }
        }



        $response['code'] = 'app_expert_retrieved_wishlist_products';
        $response['message'] = 'Wishlist products retrieved successfully.';
        $response['data'] = [
            'status' => 200,
            'items' => $products
        ];
        return $response;

    }
    
    public function add_wishlist_product(WP_REST_Request $request) {

        $user_id = get_current_user_id();
        $wishlists = \WC_Data_Store::load( 'wishlist' )->query( [ 'user_id' => $user_id] );
        if( empty( $wishlists ) ) {
            $default_wishlist = new YITH_WCWL_Wishlist();
            $default_wishlist->set_user_id( $user_id );
            $default_wishlist->save();
            do_action( 'yith_wcwl_generated_default_wishlist', $default_wishlist, $user_id );
            $wishlist_id = $default_wishlist->get_id();
        }else{
            $wishlist_id = $wishlists[0]->get_id();
        }
        $product_id = $request->get_param( 'product_id' );
        $quantity = 1;
        $position = $request->get_param( 'position' );
        $original_price = $request->get_param( 'original_price' );
        $original_currency = $request->get_param( 'original_currency' );
        $on_sale = $request->get_param( 'is_on_sale' );

        global $wpdb;

        if ( ! $product_id || ! $wishlist_id ) {
            return;
        }

        $product = wc_get_product( $product_id );
        if(empty($product)){
            return new WP_Error(
                'product_is_not_found',
                __('Product is not found.', 'app-expert'),
                array(
                    'status' => 422,
                )
            );
        }

        $wishlist = \WC_Data_Store::load( 'wishlist' )->query( [ 'id' => $wishlist_id, 'user_id' => $user_id,] );
        if(empty($wishlist)){
            return new WP_Error(
                'wishlist_is_not_found',
                __('Wishlist is not found.', 'app-expert'),
                array(
                    'status' => 422,
                )
            );
        }
        $items = $wishlist[0]->get_items();
        if(!empty($items)){
            foreach($items as $item){
                $item_product_id = $item->get_product_id();
                if($item_product_id == $request->get_param( 'product_id' )){
                    return new WP_Error(
                        'wishlist_item_is_already_exist',
                        __('Wishlist item is already exist', 'app-expert'),
                        array(
                            'status' => 422,
                        )
                    );
                }
            }
        }

        $columns = array(
            'prod_id'           => '%d',
            'quantity'          => '%d',
            'wishlist_id'       => '%d',
            'position'          => '%d',
            'original_price'    => '%f',
            'original_currency' => '%s',
            'on_sale'           => '%d',
            'dateadded'           => 'FROM_UNIXTIME( %d )'
        );
        $values  = array(
            apply_filters( 'yith_wcwl_adding_to_wishlist_product_id', $product_id ),
            apply_filters( 'yith_wcwl_adding_to_wishlist_quantity', $quantity ),
            apply_filters( 'yith_wcwl_adding_to_wishlist_wishlist_id', $wishlist_id ),
            apply_filters( 'yith_wcwl_adding_to_wishlist_position', $position ),
            apply_filters( 'yith_wcwl_adding_to_wishlist_original_price', $original_price ),
            apply_filters( 'yith_wcwl_adding_to_wishlist_original_currency', $original_currency ),
            apply_filters( 'yith_wcwl_adding_to_wishlist_on_sale', $on_sale ),
            apply_filters( 'yith_wcwl_adding_to_wishlist_date_added', time() )
        );

        if ( $user_id ) {
            $columns['user_id'] = '%d';
            $values[]           = apply_filters( 'yith_wcwl_adding_to_wishlist_user_id', $user_id );
        }

        $query_columns = implode( ', ', array_map( 'esc_sql', array_keys( $columns ) ) );
        $query_values  = implode( ', ', array_values( $columns ) );
        $query         = "INSERT INTO {$wpdb->yith_wcwl_items} ( {$query_columns} ) VALUES ( {$query_values} ) ";

        $res = $wpdb->query( $wpdb->prepare( $query, $values ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
        if ( $res ) {
            $response['code'] = 'wishlist_product_is_added_successfully';
            $response['message'] = 'Wishlists product is added successfully.';
            $response['data'] = [
                'status' => 200,
            ];
            return $response;
        }else{
            return new WP_Error(
                'wishlist_product_is_not_valid',
                __('Wishlists product is not valid', 'app-expert'),
                array(
                    'status' => 422,
                )
            );
        }

       
    }

    //todo: separate the add to a method and call it twice
    public function add_wishlist_multi_product(WP_REST_Request $request) {
        global $wpdb;
        $user_id = get_current_user_id();
        $wishlists = \WC_Data_Store::load( 'wishlist' )->query( [ 'user_id' => $user_id] );
        if( empty( $wishlists ) ) {
            $default_wishlist = new YITH_WCWL_Wishlist();
            $default_wishlist->set_user_id( $user_id );
            $default_wishlist->save();
            do_action( 'yith_wcwl_generated_default_wishlist', $default_wishlist, $user_id );
            $wishlist_id = $default_wishlist->get_id();
        }else{
            $wishlist_id = $wishlists[0]->get_id();
        }
        $quantity = 1;
        $position =null;
        $original_price =null;
        $original_currency = null;
        $on_sale = null;

        $product_ids = $request->get_param( 'product_ids' );
        $wishlist = \WC_Data_Store::load( 'wishlist' )->query( [ 'id' => $wishlist_id, 'user_id' => $user_id,] );
        if(empty($wishlist)) {
            return new WP_Error(
                'wishlist_is_not_found',
                __('Wishlist is not found.', 'app-expert'),
                array(
                    'status' => 422,
                )
            );
        }
        $items = $wishlist[0]->get_items();
        
        foreach ($product_ids as $product_id){
            $is_found = false;
            $product = wc_get_product( $product_id );
            if(empty($product)) continue;
            if(!empty($items)){
                foreach($items as $item){
                    $item_product_id = $item->get_product_id();
                    if($item_product_id == $product_id){
                        $is_found = true;
                    }
                }
            }
            if(!$is_found){
                $columns = array(
                    'prod_id'           => '%d',
                    'quantity'          => '%d',
                    'wishlist_id'       => '%d',
                    'position'          => '%d',
                    'original_price'    => '%f',
                    'original_currency' => '%s',
                    'on_sale'           => '%d',
                    'dateadded'           => 'FROM_UNIXTIME( %d )'
                );
                $values  = array(
                    apply_filters( 'yith_wcwl_adding_to_wishlist_product_id', $product_id ),
                    apply_filters( 'yith_wcwl_adding_to_wishlist_quantity', $quantity ),
                    apply_filters( 'yith_wcwl_adding_to_wishlist_wishlist_id', $wishlist_id ),
                    apply_filters( 'yith_wcwl_adding_to_wishlist_position', $position ),
                    apply_filters( 'yith_wcwl_adding_to_wishlist_original_price', $original_price ),
                    apply_filters( 'yith_wcwl_adding_to_wishlist_original_currency', $original_currency ),
                    apply_filters( 'yith_wcwl_adding_to_wishlist_on_sale', $on_sale ),
                    apply_filters( 'yith_wcwl_adding_to_wishlist_date_added', time() )
                );

                if ( $user_id ) {
                    $columns['user_id'] = '%d';
                    $values[]           = apply_filters( 'yith_wcwl_adding_to_wishlist_user_id', $user_id );
                }
                $query_columns = implode( ', ', array_map( 'esc_sql', array_keys( $columns ) ) );
                $query_values  = implode( ', ', array_values( $columns ) );
                $query         = "INSERT INTO {$wpdb->yith_wcwl_items} ( {$query_columns} ) VALUES ( {$query_values} ) ";

                $wpdb->query( $wpdb->prepare( $query, $values ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

            }
        }
        $response['code'] = 'wishlist_product_is_added_successfully';
        $response['message'] = 'Wishlists product is added successfully.';
        $response['data'] = ['status' => 200];
        return $response;
    }

    public function delete_wishlist_product(WP_REST_Request $request) {

        $user_id = get_current_user_id();
        $wishlists = \WC_Data_Store::load( 'wishlist' )->query( [ 'user_id' => $user_id] );
        if( empty( $wishlists ) ) {
            return new WP_Error(
                'no_wishlist_found',
                __('this user does not have a wishlist', 'app-expert'),
                array(
                    'status' => 404,
                )
            );
        }else{
            $wishlist_id = $wishlists[0]->get_id();
        }
        
        $items = $wishlists[0]->get_items();
        if($items){
            $is_found = false;
            foreach($items as $item){
                $product_id = $item->get_product_id();
                if($product_id == $request->get_param( 'product_id' )){
                    $is_found = true;
                    global $wpdb;

                    do_action( 'yith_wcwl_before_delete_wishlist_item', $item->get_id() );

                    $wpdb->delete( $wpdb->yith_wcwl_items, array( 'ID' => $item->get_id() ) );

                    do_action( 'yith_wcwl_delete_wishlist_item', $item->get_id() );

                    $item->set_id( 0 );
                }
            }

            if ( ! $is_found ) {
                return new WP_Error(
                    'wishlist_product_is_not_found',
                    __('Wishlists product is not found', 'app-expert'),
                    array(
                        'status' => 422,
                    )
                );
            }

        }else{
            return new WP_Error(
                'wishlist_product_is_not_found',
                __('Wishlists product is not found', 'app-expert'),
                array(
                    'status' => 422,
                )
            );
        }

        $response['code'] = 'wishlist_product_is_deleted_successfully';
        $response['message'] = 'Wishlists product is deleted successfully.';
        $response['data'] = [
            'status' => 200,
        ];
        return $response;

    }
}
