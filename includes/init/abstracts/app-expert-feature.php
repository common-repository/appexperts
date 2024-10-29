<?php
abstract class App_Expert_Feature{
    public function __construct(){
        $this->_include_files();
        $this->_include_hooks();
    }
    abstract function get_dir();
    abstract function get_current_url();
    abstract function get_current_path();
    private function _include_files()
    {
        $include_arr =array_merge(
            glob($this->get_dir()."database/*.php"),
            glob($this->get_dir()."helpers/*.php"),
            glob($this->get_dir()."backend/*.php"),
            glob($this->get_dir()."frontend/*.php"),
            glob($this->get_dir()."apis/middlewares/*.php"),
            glob($this->get_dir()."apis/modifications/*.php")
        );
        foreach($include_arr as $file){
            require_once($file);
        }
    }
    protected function _include_hooks()
    {
        add_action('rest_api_init', array($this, '_init_endpoints'));

    }
    public function _init_endpoints(){
        $include_arr = array_merge(
            glob( $this->get_dir()."apis/requests/*.php"),
            glob( $this->get_dir()."apis/endpoints/*.php"),
            glob( $this->get_dir()."apis/routes/*.php")
        );
        foreach($include_arr as $file){
            require_once($file);
        }
    }

}