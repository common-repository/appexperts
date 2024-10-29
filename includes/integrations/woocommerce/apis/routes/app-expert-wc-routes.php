<?php

class App_Expert_Wc_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes(){
        //cancel order
        App_Expert_Route::post(APP_EXPERT_API_NAMESPACE, '/orders/cancel/(?P<id>[\d]+)'      , "App_Expert_Order_Endpoint@cancel_order"         , $this->get_cancel_order_parameters(),"App_Expert_Auth_Request");
        //fetch variation by selected attrs
        App_Expert_Route::post("wc/v3", '/products/(?P<product_id>[\d]+)/fetch-variation'   , "App_Expert_Product_Endpoint@fetch_variation"     , $this->fetch_variation_params());
        App_Expert_Route::post("wc/v3", '/validate-keys'   , "App_Expert_Validate_Keys_Endpoint@validate"     , $this->validate_params());
    }

    public function get_cancel_order_parameters(){
        return array(
            'id' => array(
                'description' => __( 'Unique identifier for the order.' ),
                'type'        => 'integer',
            ),
        );
    }
    public function fetch_variation_params(){
        return [
            "product_id" => array(
                'description' => __( 'Unique identifier for the variable product.', 'woocommerce' ),
                'type'        => 'integer',
            ),
            "attributes"=>  array(
                'description'       => __( 'attributes to select specific variation', 'woocommerce' ),
                'type'              => 'Array',
                'required'          => true,
                'items'       => array(
                    'type' => 'Array',
                    'items'       => array(
                        'id' => 'integer',
                        'name' => 'string',
                        'value' => 'string',
                    ),
                ),
            ),
        ];
    }
    public function validate_params(){
        return [
            "consumer_key" => array(
                'description' => __( 'Unique identifier for the site.', 'woocommerce' ),
                'type'        => 'string',
                'required'    => true
            ),
            "consumer_secret" => array(
                'description' => __( 'Unique identifier for the site.', 'woocommerce' ),
                'type'        => 'string',
                'required'    => true
            )
        ];
    }

}

new App_Expert_Wc_Routes();

