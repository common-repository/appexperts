<?php

//todo: separate each action in a class
class App_Expert_Taxonomy_Exclude_Flag_Form{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration){
        $this->_current_integration = $_current_integration;
        add_action('admin_init', [$this, 'add_form_edits']);
        add_action('edit_term', [$this, 'save_taxonomy_exclude_flag']);
        add_action('create_term', [$this, 'save_taxonomy_exclude_flag']);
    }

    public function add_form_edits(){
        $ae_taxonomies = get_taxonomies();
        if (is_array($ae_taxonomies)) {
            foreach ($ae_taxonomies as $ae_taxonomy) {
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

    public function add_taxonomy_field(){
        
        include_once $this->_current_integration->get_current_path() . "templates/category-exclude-flag/add-taxonomy-exclude-flag.php";
    }

    public function edit_taxonomy_field($taxonomy){
        
        $flag=empty(get_term_meta($taxonomy->term_id,App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag))?false:true;
        include_once $this->_current_integration->get_current_path() . "templates/category-exclude-flag/edit-taxonomy-exclude-flag.php";
    }

    public function add_taxonomy_columns($columns){
        $new_columns = array();
        $new_columns['exclude'] = __('Exculde from AppExperts', 'app-expert');

        return array_merge( $columns, $new_columns );
    }

    public function add_taxonomy_column( $columns, $column, $id ) {
        if ( $column == 'exclude' )
            $columns = !empty(get_term_meta($id,App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag))?__('yes', 'app-expert'):__('no', 'app-expert');
        return $columns;
    }

    public function save_taxonomy_exclude_flag($term_id) {
        
        if(isset($_POST[App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag])) {
            update_term_meta($term_id,App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag,sanitize_text_field($_POST[App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag]));
        }
        else{
            if(!empty(get_term_meta($term_id,App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag)))
            {
                 delete_term_meta($term_id,App_Expert_Taxonomy_Exclude_Helper::$taxonomy_exclude_flag);
            }
            
        }
    }
}
