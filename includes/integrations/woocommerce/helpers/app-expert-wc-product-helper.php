<?php
class App_Expert_WC_Product_Helper {
    public static function get_price($product,$cost,$context='view'){
        $display_option=get_option( 'woocommerce_tax_display_shop' );
        if($context=='cart'){
            $display_option = get_option( 'woocommerce_tax_display_cart' );
        }
        if ( 'incl' === $display_option) {
            if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                $display_price = wc_get_price_including_tax($product , array( 'price' => $cost ) );
            } else {
                $display_price = $product->get_price_including_tax( 1, $cost );
            }
        } else {
            if ( function_exists( 'wc_get_price_excluding_tax' ) ) {
                $display_price = wc_get_price_excluding_tax( $product, array( 'price' => $cost ) );
            } else {
                $display_price = $product->get_price_excluding_tax( 1, $cost );
            }
        }
        return $display_price;
    }
    public static function get_prices($product,$context='view'){
        return [
            'ae_regular_price' => (float) self::get_price($product,$product->get_regular_price(),$context),
            'ae_sale_price'    => (float) self::get_price($product,$product->get_sale_price(),$context),
        ];
    }
    public static function get_terms($termOptions,$slug){
        $terms=[];
        foreach ($termOptions as $term){
            $term=get_term_by('name',$term, $slug );
            if($term) $terms[]=$term;
        }
        return !empty($terms)?$terms:null;
    }
    public static function get_attribute_slug($wc_attrs,$attr){
        /**
         * @var WC_Product_Attribute $attribute
         **/
        
        foreach($wc_attrs as $key=>$attribute){
            if(is_string($attribute)) return $key;
            $data = $attribute->get_data();
            if($data["is_taxonomy"]&&$data['id']==$attr['id']){
                return $key;
            }
        }
        return $attr['name'];
    }
    public static function get_data($product,$wc_product=null,$context='view'){
        $user_id =get_current_user_id();

        if(!$wc_product)$wc_product=wc_get_product($product['id']);
    
        //================ fix attribute label wpml translation issue & cart slug missing slug ================
        $newAttr = [];
        if(isset($product["attributes"])){
            foreach ($product["attributes"] as $attr) {
               $slug= self::get_attribute_slug($wc_product->get_attributes(),$attr);

                switch (gettype($attr)){
                    case "array":
                        $attrs=[];
                        if(isset($attr['options'])){
                            $attrs=$attr['options'];
                        }elseif (isset($attr['option'])){
                            $attrs = [$attr['option']];
                        }
                        $attr['label'] = wc_attribute_label($attr['name'], '');
                        $attr['slug']  = $slug;
                        $attr['terms'] = self::get_terms($attrs,$slug);
                        break;
                    case "object":
                        $attrs=[];
                        if($attr->options){
                            $attrs=$attr->options;
                        }elseif ($attr->option){
                            $attrs = [$attr->option];
                        }
                        $attr->label = wc_attribute_label($attr->name, '');
                        $attr->slug  = $slug;
                        $attr->terms = self::get_terms($attrs,$slug);
                        break;
                }
                $newAttr[] = $attr;
            }
            $product["attributes"] = $newAttr;
        }
        //================ fix attribute label wpml translation issue ================

        //================  review changes ================
        $product['has_bought_product']= $user_id && wc_customer_bought_product("", $user_id, $product['id']);
        $product['review_count']=$wc_product->get_review_count();
        $product['average_rating']= $wc_product->get_average_rating();
        //================  review changes ================

        //================  wishlist changes ================
        $is_wishlisted = false;
        if ($user_id&&in_array(
                'yith-woocommerce-wishlist/init.php',
                apply_filters( 'active_plugins', get_option( 'active_plugins' ) )
            )) {
            $wishlist_product = \WC_Data_Store::load('wishlist-item')->query([
                'user_id' => $user_id,
                'product_id' => $product['id'],
                'wishlist_id' => 'all',
            ] );
            if(!empty($wishlist_product)){
                $is_wishlisted = true;
            }
        }
        $product['is_wishlisted'] = $is_wishlisted;
        //================  wishlist changes ================

        //================ fix product type issue ================
        $types= get_the_terms( $product['id'], 'product_type');
        $product['type'] = !is_wp_error($types)&&!empty($types)?$types[0]->name:$wc_product->get_type();
        $product['parent_id'] = $wc_product->get_parent_id()>0?$wc_product->get_parent_id():null;
        //================  fix product type issue  ================

        //================  prices fix ================
        $prices=self::get_prices($wc_product,$context);
        return apply_filters('ae_rest_product_obj',array_merge($product,$prices),$wc_product);
        //================  prices fix ================
    }
}