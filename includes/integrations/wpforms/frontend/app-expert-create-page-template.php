<?php
class App_Expert_WPForms_Page{
    private $_current_integration;
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;
        add_filter( 'theme_page_templates',array($this, 'add_page_template_to_dropdown' ));
        add_filter( 'template_include', array($this,'assign_page_template'), 99 ,1);

    }
    public function add_page_template_to_dropdown($templates){
        $templates[$this->_current_integration->get_current_path() . 'templates/wpforms-form-template.php'] = __('APPExpert WPForms Template', 'text-domain');
        return $templates;
        
    }

    public function assign_page_template($template){
        global $post;
        $slug=$post->post_name;
        if (is_page()&&$slug==MOBILE_WPFORMS_PAGE_SLUG){
            $template =$this->_current_integration->get_current_path() . 'templates/wpforms-form-template.php';
        }
    return $template;
}

}