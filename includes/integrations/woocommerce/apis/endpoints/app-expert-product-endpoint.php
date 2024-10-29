<?php

class App_Expert_Product_Endpoint extends WC_REST_Products_Controller
{
    public function fetch_variation(WP_REST_Request $request){
        $product_id=$request->get_param('product_id');
        $prod = wc_get_product($product_id);
        $product_attributes=$prod->get_attributes();
        $attrs=$request->get_param('attributes');
        $attributes=[];
        // $attrs is required no need for isset & default from get_param is null
        // not empty do isset inside too
        if(!empty($attrs)){
            foreach($product_attributes as $key=>$product_attr){
                foreach($attrs as $attr){
                    if($product_attr->get_id()==$attr['id']){
                        $term=get_term_by('name',$attr['value'],$key);
                        if($term){
                        $attributes['attribute_'.$key]=$term->slug;
                        }else{ // custom attribute
                        $attributes['attribute_'.$key]=$attr['value'];
                        }
                        // break;
                    }
                }
            }
        }
        $_store=new \WC_Product_Data_Store_CPT();
        $variation_id= $_store->find_matching_product_variation($prod, $attributes);
        if($variation_id>0){
            $new_req=new WP_REST_Request();
            $new_req->set_param('id',$variation_id);
            return $this->get_item($new_req);
        }
        //todo:Enhancement use App_Expert_Response
        return new WP_Error("invalid_selection",__("No product found with the selected values",'woocommerce'),["status"=>422]);
    }
}