<?php
class App_Expert_Custom_Checkout
{
    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;
        // Render custom checkout page
        add_filter('template_include', array($this, 'render_checkout_template'), 12, 1);
        add_action('template_include', array($this, 'render_thank_you_page'), 12, 1);

        add_action('template_redirect', array($this, 'add_products_to_cart'), 10, 1);
        add_action('woocommerce_thankyou', array($this, 'redirect_thank_you_page'), 10, 1);


        //fix lang in checkout
        // add_filter('locale', array($this, 'force_set_lang'),100);
        add_filter('wc_get_template', array($this, 'render_table'), 1,5);

        // Delete all woocommerce styles from custom checkout page
        add_action('wp_enqueue_scripts',  array($this, 'delete_theme_styles'), 20);
        add_action('wp_enqueue_scripts',  array($this, 'add_custom_pages_styles'), 20);
        add_filter( 'body_class',array($this,'add_checkout_body_classes' ),10);
        add_action('woocommerce_before_checkout_form',array($this,'add_loader'));
    }



    public function redirect_thank_you_page($order_id)
    {
        $isWebview = false;
        if ((strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile/') !== false) && (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari/') == false)) {
            $isWebview = true;
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            $isWebview = true;
        }
        if ($isWebview) {
            $thankyou_page = @get_page_by_path(MOBILE_THANK_YOU_PAGE_SLUG);
            $url = "?order_id=$order_id";
            if(isset($_COOKIE["ae_locale"])){
                $url.="&ae_display_lang={$_COOKIE["ae_locale"]}";
            }
            wp_redirect(get_permalink($thankyou_page->ID) .$url );
            exit;
        }
    }


    public function render_thank_you_page($template){
        if (App_Expert_app_Custom_Checkout_Helper::is_thank_you_custom_page()) {
            return $this->_current_integration->get_current_path() . "templates/thank-you.php";
        }

        return $template;
    }

    public function render_checkout_template($template)
    {
        global $post;
         if ($post->post_name==MOBILE_CHECKOUT_PAGE_SLUG&&App_Expert_app_Custom_Checkout_Helper::check_page($post->post_name)) {
            return $this->_current_integration->get_current_path() . "templates/clean-page.php";
         }
        return $template;
    }


    function delete_theme_styles()
    {
        if (
                !App_Expert_app_Custom_Checkout_Helper::is_checkout_custom_page() &&
                !App_Expert_app_Custom_Checkout_Helper::is_thank_you_custom_page()
        ) {
            return;
        }

        global $wp_styles;
        global $wp_scripts;

        $wp_get_theme = wp_get_theme();

        $child_theme  = $wp_get_theme->get_stylesheet();
        $parent_theme = $wp_get_theme->get_template();

        foreach ($wp_styles->registered as $key => $value) {
            $src = $value->src;

            // Woocommerce special theme handling
            if (strpos($src, "woocommerce/assets/css/" . $parent_theme) !== false) {
                unset($wp_styles->registered[$key]);
            }

            if (strpos($src, "woocommerce/assets/css/" . $child_theme) !== false) {
                unset($wp_styles->registered[$key]);
            }

            // Twenty themes
            if (strpos($src, "woocommerce/assets/css/twenty") !== false) {
                unset($wp_styles->registered[$key]);
            }

            // Themes assets
            if (strpos($src, "themes/") !== false) {
                unset($wp_styles->registered[$key]);
            }

            if (strpos($src, "themes/$child_theme/") !== false || strpos($src, "themes/$parent_theme/") !== false) {
                unset($wp_styles->registered[$key]);
            }

            if (strpos($src, "/uploads/$child_theme/") !== false || strpos($src, "/uploads/$parent_theme/") !== false) {
                unset($wp_styles->registered[$key]);
            }
        }

        foreach ($wp_scripts->registered as $key => $value) {
            $src = $value->src;

            if (strpos($src, "themes/") !== false) {
                unset($wp_styles->registered[$key]);
            }

            if (strpos($src, "themes/$child_theme/") !== false || strpos($src, "themes/$parent_theme/") !== false) {
                unset($wp_scripts->registered[$key]);
            }

            if (strpos($src, "/uploads/$child_theme/") !== false || strpos($src, "/uploads/$parent_theme/") !== false) {
                unset($wp_scripts->registered[$key]);
            }
        }
    }


    /**
     * Enqueue style pages for woocommerce
     */
    function add_custom_pages_styles()
    {
        if (App_Expert_app_Custom_Checkout_Helper::is_checkout_custom_page()){
            wp_enqueue_style('app-expert-custom-checkout-style', $this->_current_integration->get_current_url() . 'assets/css/woocommerce.css', false, APP_EXPERT_PLUGIN_VERSION, 'all');
            wp_enqueue_script('app-expert-custom-checkout-script', $this->_current_integration->get_current_url() . 'assets/js/woocommerce.js', false, APP_EXPERT_PLUGIN_VERSION, 'all');

        }elseif(App_Expert_app_Custom_Checkout_Helper::is_thank_you_custom_page() ) {
            wp_enqueue_style('app-expert-custom-checkout-style', $this->_current_integration->get_current_url() . 'assets/css/woocommerce-thankyou.css', false, APP_EXPERT_PLUGIN_VERSION, 'all');
        }

    }

    function add_products_to_cart()
    {
        if (!function_exists('WC')) return;


        if (!App_Expert_app_Custom_Checkout_Helper::is_checkout_custom_page()) return;


        // Product details has the format:
        // product1_id,variation1_id,quantity1,variation_name:variation_value;;variation_name:variation_value||product2_id,...

        if(!isset($_GET['products'])) return;
        $products = $_GET['products'] ? explode("||", urldecode($_GET['products'])) : [];
        foreach ($products as $product) {
            WC()->cart->empty_cart();
            $product_details = explode(",", $product);
            $product_id = intval($product_details[0]);
            $variation_id = intval($product_details[1]);
            $quantity = intval($product_details[2]);
            $variations = isset($product_details[3]) ? explode(";;", $product_details[3]) : [];
            $variationsData = [];

            // Get variation data key value
            foreach ($variations as $sent_data) {
                $variation = explode(":", $sent_data);

                $taxonomyName = wc_attribute_taxonomy_name( $variation[0] );
                $is_exist = taxonomy_exists( $taxonomyName );
                if($is_exist){
                    $value =$variation[1];
                    $term =  get_term_by('name', $value,$taxonomyName);
                    if($term) $value = $term->slug;

                    $variationsData['attribute_' . $taxonomyName ] = $value;
                }else{
                    $variationsData['attribute_' . sanitize_title($variation[0])] = $variation[1];
                }
            }

            WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variationsData);
        }

    }

    public function force_set_lang($locale){
       // if(!App_Expert_app_Custom_Checkout_Helper::is_checkout_custom_page()) return $locale;
        if(is_admin())return $locale;
        if(isset($_GET['ae_display_lang']))
        {
            return $_GET['ae_display_lang'];
        }
        else if(isset($_COOKIE["ae_locale"]))
        {
            return $_COOKIE["ae_locale"];
        }

        return $locale;
    }

    public function force_switch_lang(){
        if(!App_Expert_app_Custom_Checkout_Helper::is_checkout_custom_page()) return;
        if(isset($_GET['ae_display_lang']))
        {
            $_GET['lang'] =$_GET['ae_display_lang'];
            App_Expert_Language::switch_lang($_GET['ae_display_lang']);
        }
        if(isset($_GET['ae_locale']))
        {
            $_GET['lang'] =$_GET['ae_locale'];
            App_Expert_Language::switch_lang($_GET['ae_locale']);
        }
        else if(isset($_COOKIE["ae_locale"]))
        {
            $_GET['lang'] =$_COOKIE['ae_locale'];
            App_Expert_Language::switch_lang($_COOKIE["ae_locale"]);
        }
    }

    public function render_table($template, $template_name, $args, $template_path, $default_path){
        if($template_name=="checkout/review-order.php"){
            $this->force_switch_lang();
        }
        return $template;
    }
    public function add_checkout_body_classes($classes){
        if(App_Expert_app_Custom_Checkout_Helper::is_checkout_custom_page()){
            $classes[] = ' cleanpage';
        }
        return $classes;
    }
    public function add_loader(){
        if(App_Expert_app_Custom_Checkout_Helper::is_checkout_custom_page()){
        ?>
            <div id="loader-wrapper">
                <div id="loader"></div>
            </div>
            <?php
        }
    }
}