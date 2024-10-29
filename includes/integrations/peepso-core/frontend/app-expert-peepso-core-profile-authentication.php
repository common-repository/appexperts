<?php
class App_Expert_Peepso_Core_Profile_Authentication extends App_Expert_Webview_Authentication {
    private $_current_integration;

    public function __construct(App_Expert_Integration $_current_integration){
        parent::__construct();
        $this->_current_integration = $_current_integration;
    }

    public function isAllowed(){
        $PeepSoUser  = PeepSoUser::get_instance();
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_uri = str_replace($_SERVER["QUERY_STRING"],'',$request_uri);
        return !!strpos($PeepSoUser->get_profileurl(),$request_uri);
    }
}

