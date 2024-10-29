<?php

//todo: separate each action in a class
class App_Expert_Taxonomy_image_form{

    private $_current_integration;
    private $aeci_placeholder;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        $this->aeci_placeholder =  $this->_current_integration->get_current_url() . 'assets/images/placeholder.png';
        add_action('admin_init', [$this, 'add_form_edits']);
        // Register styles and scripts
        if ( strpos( $_SERVER['SCRIPT_NAME'], 'edit-tags.php' ) > 0 || strpos( $_SERVER['SCRIPT_NAME'], 'term.php' ) > 0 ) {
            add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
          //  add_action('quick_edit_custom_box', [$this, 'get_quick_edit_custom_box'], 10, 3);
        }
        add_action('edit_term', [$this, 'save_taxonomy_image']);
        add_action('create_term', [$this, 'save_taxonomy_image']);
    }

    public function add_form_edits(){
        $ae_taxonomies = get_taxonomies();
        if (is_array($ae_taxonomies)) {
            $aeci_options = get_option('aeci_options');

            if (!is_array($aeci_options))
                $aeci_options = array();

            if (empty($aeci_options['included_taxonomies']))
                $aeci_options['included_taxonomies'] = array();

            foreach ($ae_taxonomies as $ae_taxonomy) {
                if (in_array($ae_taxonomy, $aeci_options['included_taxonomies'])) {
                    add_action($ae_taxonomy.'_add_form_fields', [$this, 'add_taxonomy_field']);
                    add_action($ae_taxonomy.'_edit_form_fields', [$this, 'edit_taxonomy_field']);
                    add_filter('manage_edit-'.$ae_taxonomy.'_columns', [$this, 'add_taxonomy_columns']);
                    add_filter('manage_'.$ae_taxonomy.'_custom_column', [$this, 'add_taxonomy_column'], 10, 3 );

                    // If tax is deleted
                    add_action("delete_{$ae_taxonomy}", function($tt_id) {
                        delete_option('ae_taxonomy_image'.$tt_id);
                    });
                }
            }
        }
    }

    public function add_taxonomy_field(){
        if (get_bloginfo('version') >= 3.5){
            wp_enqueue_media();
        } else {
            wp_enqueue_style('thickbox');
            wp_enqueue_script('thickbox');
        }
        include_once $this->_current_integration->get_current_path() . "templates/category-images/add-taxonomy-image-field.php";
    }

    public function edit_taxonomy_field($taxonomy){
        if (get_bloginfo('version') >= 3.5){
            wp_enqueue_media();
        } else {
            wp_enqueue_style('thickbox');
            wp_enqueue_script('thickbox');
        }

        $image_url = App_Expert_Taxonomy_Image_Helper::get_taxonomy_image_url( $taxonomy->term_id, NULL, TRUE );

        include_once $this->_current_integration->get_current_path() . "templates/category-images/edit-taxonomy-image-field.php";
    }

    public function add_taxonomy_columns($columns){
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['thumb'] = __('Image', 'app-expert');

        unset( $columns['cb'] );

        return array_merge( $new_columns, $columns );
    }

    public function add_taxonomy_column( $columns, $column, $id ) {
        if ( $column == 'thumb' )
            $columns = '<span><img src="' . App_Expert_Taxonomy_Image_Helper::get_taxonomy_image_url($id, 'thumbnail', TRUE) . '" alt="' . __('Thumbnail', 'app-expert') . '" class="wp-post-image" /></span>';
        return $columns;
    }


    public function register_scripts(){
        wp_enqueue_style( 'app_expert_taxonomy_images_styles',  $this->_current_integration->get_current_url() . 'assets/css/aeci-styles.css',[],APP_EXPERT_PLUGIN_VERSION);
        wp_enqueue_script('app_expert_taxonomy_images_scripts',$this->_current_integration->get_current_url() . 'assets/js/aeci-scripts.js',[],APP_EXPERT_PLUGIN_VERSION,true);

        $aeci_js_config = [
            'wordpress_ver' => get_bloginfo("version"),
            'placeholder' => $this->aeci_placeholder
        ];
        wp_localize_script('app_expert_taxonomy_images_scripts', 'aeci_config', $aeci_js_config);

    }

    public function get_quick_edit_custom_box($column_name, $screen, $name) {
        if ($column_name == 'thumb')
           include_once $this->_current_integration->get_current_path() ."templates/category-images/add-taxonomy-image-quick-edit-field.php";
    }
    public function save_taxonomy_image($term_id) {
        if(isset($_POST['aeci_taxonomy_image'])) {
            update_option('ae_taxonomy_image'.$term_id, sanitize_text_field($_POST['aeci_taxonomy_image']), false);
        }
    }
}
