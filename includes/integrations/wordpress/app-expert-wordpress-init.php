<?php
Class App_Expert_Wordpress_Init extends App_Expert_Integration {
    function get_dir(){
        return plugin_dir_path(__FILE__);
    }
    public function _include_hooks() {
        parent::_include_hooks();
        $this->_inst_classes();
        register_activation_hook(APP_EXPERT_FILE, array($this, 'add_custom_render_page'));
        add_action( 'rest_api_init', array($this,'appexpert_rest_cors'), 15 );
        add_filter( 'rest_pre_serve_request',array($this,'appexpert_pre_serve_requests'),15);


    }
    public function _inst_classes(){
      //backend classes
      new App_Expert_Taxonomy_Image_Settings($this);
      new App_Expert_Taxonomy_image_form($this);
      new App_Expert_Taxonomy_Exclude_Flag_Form($this);
      //frontend classes
      new App_Expert_Page_Manager($this);
    }
    public function get_current_url(){
        return  APP_EXPERT_URL."includes/integrations/wordpress/";

    }
    public function get_current_path(){
        return APP_EXPERT_PATH."includes/integrations/wordpress/";
    }
    public function get_endponints_files(){
        /**
         * Add WordPress api just wrapped in app-expert name
         * To:
         *  - unify response structure
         *  - add extra data according to app needs
         **/
        return array_merge(
            parent::get_endponints_files(),
//            glob($this->get_dir()."apis/response-meta-data/blocks-meta/*.php"),
            glob($this->get_dir()."apis/response-meta-data/*.php"),
            glob($this->get_dir()."apis/wrapped-apis/*.php")
        );
    }
    public function add_custom_render_page(){
        // Add custom page renderer page
        if (!get_page_by_path(PAGE_RENDERER_SLUG)) {
            $post_details = array(
                'post_title'    => 'App Experts Page Renderer',
                'post_content'  => 'You are not authorized to access this page directly',
                'post_status'   => 'publish',
                'post_author'   => 1,
                'post_type' => 'page',
                'post_slug' => PAGE_RENDERER_SLUG
            );
            wp_insert_post($post_details);
        }

    }

    function get_dependencies()
    {
        return [];
    }

    public function add_logs($arr)
    {
        $arr[] ='wordpress';
        return $arr;
    }
    public function appexpert_rest_cors(){
        remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
          
    }
    /**
     * Solve CROS origin issue solution based on 
     * https://wordpress.org/support/topic/woocommerce-plugin-and-cors-errors/
     * https://litzdigital.com/blog/how-to-configure-cors-for-the-wordpress-rest-api/
     */
    public function appexpert_pre_serve_requests($value){
        $domain=get_option('appexperts_domain');
        if(isset($_SERVER['HTTP_ORIGIN'])){
            $http_origin = $_SERVER['HTTP_ORIGIN'];
            if(!$domain){
                $domain=array('https://app.appexperts.io','https://previewer.appexperts.io');
                update_option('appexperts_domain',$domain);
            }
            if (is_array($domain)&&in_array($http_origin,$domain)) {
                header( "Access-Control-Allow-Origin: $http_origin" );
                header( 'Access-Control-Allow-Methods: GET' );
            }
        }    
        return $value;
    }
    public function should_include_files(){
        return true;
    }
}
//todo:find a better way to create new object
new App_Expert_Wordpress_Init();