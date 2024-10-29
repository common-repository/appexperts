<?php
class App_Expert_Taxonomy_Exclude_Helper {
    
    // get excluded terms ids
    public static $taxonomy_exclude_flag='ae_taxonomy_exclude_flag';
    //@TODO revist this part.
    public static $product_taxonomies = array(
        'product_cat'            ,
        'product_tag'            ,
        'product_shipping_class' ,
        'product_type'           ,
    );
    public static $post_taxonomies = array(
        'category' ,
        'post_tag'      ,
    );

    public static function get_post_product_taxonomies() {
        if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
            return array_merge(self::$post_taxonomies,self::$product_taxonomies);
        }else{
            return self::$post_taxonomies;
        }
    }
    public static function get_excluded_terms_ids($taxonomy) {
        $terms_ids=[];
        $args_term = array(
            'hide_empty' => false,
            'meta_query' => array(
                array(
                   'key'       => App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag,
                   'value'     => 'true',
                )
            ),
            'taxonomy'  => $taxonomy,
            'fields'   => 'ids',
            );
        $terms_ids = get_terms( $args_term );
        return $terms_ids;
        
    }
}
