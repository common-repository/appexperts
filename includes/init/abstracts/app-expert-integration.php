<?php
abstract class App_Expert_Integration{
    public function __construct() {
        $this->includes();
    }
    public function includes(){
        if( !$this->is_classes_dependencies()||
            !$this->is_dependencies_allowed() ||
            !$this->is_dependencies_active()
        )  return;

        $this->_include_files();
        $this->_include_hooks();
        add_filter('ae_daily_log_custom_integration',array($this,"add_logs"));
    }

    abstract public function get_dir();
    abstract public function get_current_url();
    abstract public function get_current_path();
    abstract public function get_dependencies();
    abstract public function add_logs($arr);
    public function is_classes_dependencies(){
        return true;
    }
    private function _include_files(){
        $include_arr = $this->get_files();
        foreach($include_arr as $file){
            require_once($file);
        }
    }
    private function is_dependencies_active(){
        $dependencies = $this->get_dependencies();
        if(empty($dependencies)) return true;
        //todo: file name could be changed .
        //class_exists check?
        $active_plugins = apply_filters('active_plugins', get_option('active_plugins'));
        foreach ($dependencies as$dependency)
        {
            if (!in_array($dependency, $active_plugins)){
                return false;
            }
        }
        return true;
    }

    protected function _include_hooks(){
        add_action('rest_api_init', array($this, '_init_endpoints'));
    }

    public function _init_endpoints(){
        $include_arr = $this->get_endponints_files();
        foreach($include_arr as $file){
            require_once($file);
        }
    }
    public function get_files(){
        return array_merge(
            glob($this->get_dir()."database/*.php"),
            glob($this->get_dir()."helpers/*.php"),
            glob($this->get_dir()."backend/*.php"),
            glob($this->get_dir()."frontend/*.php"),
            glob($this->get_dir()."apis/middlewares/*.php"),
            glob($this->get_dir()."apis/serializers/*.php"),
            glob($this->get_dir()."apis/modifications/*.php")
        );
    }
    public function get_endponints_files(){
        return array_merge(
            glob( $this->get_dir()."apis/requests/*.php"),
            glob( $this->get_dir()."apis/endpoints/*.php"),
            glob( $this->get_dir()."apis/routes/*.php")
        );
    }

    public function is_dependencies_allowed()
    {
        return true;
    }
}