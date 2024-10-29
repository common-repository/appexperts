<?php
class App_Expert_Render_Form{
    public function __construct(){
        add_action('init',array($this,'create_wpforms_custom_page'));
        add_action('wp_enqueue_scripts',  array($this, 'add_wpforms_style'), 20);   
        add_filter( "wpml_forms_".MOBILE_WPFORMS_PAGE_SLUG."_package", array( $this, 'translate_aewpforms_custom_page' ), 10, 2 );
        add_filter('wpforms_stripe_api_payment_intents_set_config_element_style',array($this,'add_stripe_field_style'));

    }
    public function create_wpforms_custom_page()
    {
    if (!get_page_by_path(MOBILE_WPFORMS_PAGE_SLUG)) {

        $post_details = array(
            'post_title'    => 'Mobile App Wpforms Page',
            'post_content'  => ' test',
            'post_status'   => 'publish',
            'post_author'   => 1,
            'post_type' => 'page',
            'post_name' =>MOBILE_WPFORMS_PAGE_SLUG
        );
        wp_insert_post($post_details);
        }
    }
   
    public function translate_aewpforms_custom_page($package, $formId){
        $package['load_aewpforms'] = esc_url(
			add_query_arg(
				array( 'id' => $formId ),
				get_permalink( get_page_by_path( MOBILE_WPFORMS_PAGE_SLUG ) )
			)
		);
        return  $package;
    }
  

    public function add_wpforms_style(){
        global $post;
        $slug=$post->post_name;
        if (is_page()&&$slug==MOBILE_WPFORMS_PAGE_SLUG){
            wp_enqueue_style('ae-wpforms-mobile-form', APP_EXPERT_URL.'/includes/integrations/wpforms/assets/css/ae-wpforms-mobile-form.css', false, '1.0', 'all');
            wp_enqueue_script('ae-wpforms-mobile-form-js', APP_EXPERT_URL.'/includes/integrations/wpforms/assets/js/ae-wpforms-mobile-form.js', false, '1.0', 'all');
            $colors=array(
                'main_color'=>$_GET['main-color'],
                'text_color'=>$_GET['text-color'],
                'secondary_color'=>$_GET['secondary-color'],
                'primary_btn_text_color'=>$_GET['primary-btn-text-color']
            );
                wp_localize_script('ae-wpforms-mobile-form-js','colors',$colors);
        }
    }
    public function add_stripe_field_style($element_style ){
       
        $element_style = [
            'base' => [
                'iconColor' => '#b95d52',
                'fontFamily' => 'Roboto, sans-serif',
                'fontSize' => '16px',
                'fontWeight' => '100',
                'backgroundColor' => '#f6f6f6',
                '::placeholder' => [
                        'color' => '#b95d52',
                        'font-family' => 'Roboto, sans-serif',
                        'font-size' => '16px',
                        'font-weight' => '100',
                ]
            ],
        ];
         
        return $element_style;     

         
    }
    
}