<?php
class App_Expert_Taxonomy_Image_Settings{

    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;

        add_filter('ae_settings_tabs',array($this, 'add_category_image_tab'), 10, 1);
        add_filter('ae_default_settings_tab',array($this, 'set_default_settings_tab'), 10, 1);
        add_action('admin_init', [$this, 'register_settings']);
        add_filter('ae_daily_log_custom_settings',array($this,"add_logs"));

    }

    public function register_settings(){
        register_setting('aeci_options', 'aeci_options');
        add_settings_section('App Experts', __('Categories Images settings', 'app-expert'), [$this, 'get_section_text'], 'aeci-options');
        add_settings_field('ae_included_taxonomies', __('Taxonomies', 'app-expert'), [$this, 'get_section_inputs'], 'aeci-options', 'App Experts');

    }
    public function get_section_text()
    {
        echo '<p>'.__('Please select the taxonomies that you want them to get displayed with images within the mobile application pages.', 'app-expert').'</p>';
    }
    public function get_section_inputs()
    {
        $options = get_option('aeci_options');
        $disabled_taxonomies = ['nav_menu', 'link_category', 'post_format'];
        foreach (get_taxonomies() as $tax) {
            if (in_array($tax, $disabled_taxonomies)) continue;
            ?>
            <input type="checkbox" name="aeci_options[included_taxonomies][<?php echo $tax ?>]" value="<?php echo $tax ?>" <?php checked(isset($options['included_taxonomies'][$tax])); ?> /> <?php echo $tax ;?><br />
        <?php }
    }
    public function add_category_image_tab ($tabs)
    {
        $tabs=array_merge(['settings'=> [
            "tab_name"=>  __( 'settings', 'app-expert' ),
            "tab_view"=>  $this->_current_integration->get_current_path() . "templates/settings-tabs/taxonomy-image-settings.php"
        ]],$tabs);
        return $tabs;
    }
    public function set_default_settings_tab($tab){
        return "settings";
    }
    public function add_logs($settings_array){
        $settings_array['taxonomy_images']=   get_option('aeci_options');
        return $settings_array;
    }
}
