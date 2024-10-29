<?php
class App_Expert_Admin_Menu{
   private $_current_feature;
   public function __construct(App_Expert_Feature $_current_feature)
   {
       $this->_current_feature = $_current_feature;
       add_action('admin_menu', [$this, 'add_settings_page']);
       add_action('admin_head', array($this, 'add_custom_style'));

   }
    public function add_settings_page()
    {
        add_menu_page( __( 'App Experts Settings','app-expert'), __( 'APPExperts', 'app-expert')  , 'manage_options'         , 'app_expert',null,$this->_current_feature->get_current_url().'assets/images/logo.svg', 100);
    }
    //todo:can it move a separate css file ?? or is it better this way?
    //todo: remove css & add img with right size ?
    public function add_custom_style(){
        echo '<style>#toplevel_page_app_expert.wp-menu-image img{width: 80%;padding:5px};</style>';
        echo '<style>#toplevel_page_app_expert .wp-menu-image  img{width: 80%;padding:5px};</style>';
    }

}