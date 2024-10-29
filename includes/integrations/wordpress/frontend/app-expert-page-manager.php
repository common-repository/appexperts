<?php

class App_Expert_Page_Manager
{


    const PAGE_TEMPLATE_COCOBASIC_THEME = 'cocobasic-shortcode';
    const PAGE_TEMPLATE_MEELO_WP_THEME = 'meelo-wp';
    const PAGE_TEMPLATE_ELEMENTOR_THEME = 'elementor';

    const PAGE_MOBILE_PREVIEW_QUERY_VAR = 'appexperts_mobile_preview';


    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;
        // Custom page preview for webview
        add_action('pre_get_posts', array($this, 'change_wp_page_alter_query'));


        // Render custom checkout page
        add_filter('template_include', array($this, 'render_webview_page'), 12, 1); // After woocoomerce and elementor

        // Add custom style
        add_action('wp_enqueue_scripts', [$this, 'enqueue_custom_page_style']);
        add_filter( "ae_settings_data", array($this,'add_additional_params_to_settings'), 10, 1 );

    }


    function change_wp_page_alter_query($query)
    {

        if (!$this->is_plugin_page_renderer_page() ||  !$query->is_main_query()) {
            return;
        }

        // Get the other page to display, will be sent in url in 'display_page_id' parameter
        $page_id = intval($_GET['display_page_id']);
        if (!$page_id) {
            return;
        }

        // add WPML support
        // if (in_array('sitepress-multilingual-cms/sitepress.php', apply_filters('active_plugins', get_option('active_plugins')))) {
        //     if (isset($_GET['appexpertlang']) && $_GET['appexpertlang']) {
        //         $page_id = apply_filters('wpml_object_id', $page_id, 'page', true, $_GET['appexpertlang']);
        //     }
        // }

        $query->set('post_type', 'page');
        $query->set('page_id', $page_id);
    }


    public function enqueue_custom_page_style()
    {
        if (!$this->is_plugin_page_renderer_page() && !$this->is_page_for_mobile_preview()) {
            return;
        }

        wp_enqueue_style('app-expert-menu-style', APP_EXPERT_URL . 'assets/css/menu-styles.css', false, '1.0', 'all');
    }


    public function render_webview_page($template)
    {

        if ($this->is_plugin_page_renderer_page()) {

            // if (   str_contains($template, self::PAGE_TEMPLATE_COCOBASIC_THEME)
            //     || str_contains($template, self::PAGE_TEMPLATE_ELEMENTOR_THEME)
            //     || str_contains($template, self::PAGE_TEMPLATE_MEELO_WP_THEME)
            // ) {

                global $wpml_url_converter;
                $lang=apply_filters( 'wpml_current_language', null );
                $permalink = apply_filters('ae_page_webview_permalink',get_the_permalink());
                $permalink = apply_filters('wpml_permalink', $permalink, $lang, true);
                // $permalink = $wpml_url_converter->convert_url( $permalink , 'ar' );
                // $translated_pages[$code]=get_permalink(get_page_by_path( MOBILE_WPFORMS_PAGE_SLUG ));
                // $translated_pages[$value['code']]=$wpml_url_converter->convert_url( get_permalink( get_page_by_path( $slug ) ), $value['code'] );
                // $translated_page=apply_filters('ae_wpml_translated_pages',$translated_pages,MOBILE_WPFORMS_PAGE_SLUG);
               
                // Apply localization if exists
                // $lang = "en";
                // $lang = $lang ?: "en";

                // $permalink = add_query_arg([
                //     'lang' => $lang,
                //     self::PAGE_MOBILE_PREVIEW_QUERY_VAR => '1',
                // ], $permalink);

                wp_redirect($permalink);
                exit;
            // }


            return  $this->_current_integration->get_current_path() . "templates/clean-page.php";
        }

        return $template;
    }



    /**
     * Check if the current page visited is the plugin page renderer page
     *
     * @return bool
     */
    protected function is_plugin_page_renderer_page()
    {
        $appexpert_rendere_page = @get_page_by_path(PAGE_RENDERER_SLUG);
        if (!$appexpert_rendere_page || is_admin()) {
            return false;
        }

        $request_uri = $_SERVER['REQUEST_URI'];
        return (false !== strpos($request_uri, PAGE_RENDERER_SLUG));
    }

    /**
     * Check if the current page visited is from our page renderer
     *
     * @return bool
     */
    protected function is_page_for_mobile_preview()
    {
        return isset($_GET[self::PAGE_MOBILE_PREVIEW_QUERY_VAR]) && !empty($_GET[self::PAGE_MOBILE_PREVIEW_QUERY_VAR]);
    }
    public function add_additional_params_to_settings($data){
        $lang_code=get_bloginfo("language");
        $lang_code=explode('-',$lang_code);
        $code= $lang_code[0];
        $translated_pages=[];
        $translated_pages[$code]=get_permalink(get_page_by_path( PAGE_RENDERER_SLUG ));
        $translated_page=apply_filters('ae_wpml_translated_pages',$translated_pages,PAGE_RENDERER_SLUG);
       
        $data['general']['translated_page']=$translated_page;
         return $data;
    }
}
