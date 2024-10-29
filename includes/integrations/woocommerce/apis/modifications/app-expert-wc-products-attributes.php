<?php

class App_Expert_WC_Products_Attributes
{
    public function __construct() {
        add_filter( 'woocommerce_rest_prepare_product_attribute', array( $this, 'add_attribute_edits' ),20,3);
    }
    public function add_attribute_edits($response, $attribute,$req){
        if( empty( $response->data ) )
            return $response;

        $response->data['label']=wc_attribute_label($response->data['name'],'');
        return $response;
    }
}

new App_Expert_WC_Products_Attributes();
