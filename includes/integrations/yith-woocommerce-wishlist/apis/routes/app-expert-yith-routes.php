<?php

class App_Expert_Yith_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        App_Expert_Route::get(YITH_API_NAMESPACE, '/wishlist', "App_Expert_Yith_Endpoint@get_wishlists" ,array(),"App_Expert_Auth_Request");

        App_Expert_Route::get(YITH_API_NAMESPACE, '/wishlist/products', "App_Expert_Yith_Endpoint@get_wishlist_products" , $this->get_wishlist_parameters(),"App_Expert_Auth_Request");

        App_Expert_Route::post(YITH_API_NAMESPACE, '/wishlist/add-product', "App_Expert_Yith_Endpoint@add_wishlist_product" , $this->add_wishlist_product_parameters(),"App_Expert_Auth_Request");

        App_Expert_Route::post(YITH_API_NAMESPACE, '/wishlist/sync-product', "App_Expert_Yith_Endpoint@add_wishlist_multi_product" , $this->add_wishlist_multi_product_parameters(),"App_Expert_Auth_Request");

        App_Expert_Route::delete(YITH_API_NAMESPACE, '/wishlist/delete-product', "App_Expert_Yith_Endpoint@delete_wishlist_product" , $this->get_remove_wishlist_product_parameters(),"App_Expert_Auth_Request");

    }

    public function get_wishlist_parameters(){
        return  array(
                'page'     => array(
                    'description'       => __( 'Current page of the collection.' ),
                    'type'              => 'integer',
                    'default'           => 1,
                    'sanitize_callback' => 'absint',
                    'validate_callback' => 'rest_validate_request_arg',
                    'minimum'           => 1,
                ),
                'per_page' => array(
                    'description'       => __( 'Maximum number of items to be returned in result set.' ),
                    'type'              => 'integer',
                    'default'           => 0,
                    'minimum'           => 0,
                    'maximum'           => 100,
                    'sanitize_callback' => 'absint',
                    'validate_callback' => 'rest_validate_request_arg',
                ),

        );
    }

    public function get_remove_wishlist_product_parameters(){
        return  array(
            'product_id'        => array(
                'description' => __( 'Product Id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )

        );
    }

    public function add_wishlist_product_parameters(){
        return  array(
            'product_id'        => array(
                'description' => __( 'Product Id' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'position'        => array(
                'description' => __( 'Position' , 'app-expert' ),
                'type'        => 'integer',
                'default'        => 0,
                'required' => false
            ),
            'original_price'        => array(
                'description' => __( 'Original Price' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'original_currency'        => array(
                'description' => __( 'Original Currency' , 'app-expert' ),
                'type'        => 'string',
                'default'        => 'EGP',
                'required' => false
            ),
            'is_on_sale'        => array(
                'description' => __( 'Is on Sale' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            )

        );
    }

    public function add_wishlist_multi_product_parameters()
    {
        return  array(
            'product_ids'        => array(
                'description' => __( 'Array of Product Ids' , 'app-expert' ),
                'type'        => 'Array',
                'required' => true
            )
        );
    }
}

new App_Expert_Yith_Routes();

