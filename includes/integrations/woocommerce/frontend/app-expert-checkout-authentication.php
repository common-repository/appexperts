<?php
class App_Expert_Checkout_Authentication extends App_Expert_Webview_Authentication {
    private $_current_integration;

    public function __construct(App_Expert_Integration $_current_integration){
        parent::__construct();
        $this->_current_integration = $_current_integration;
    }

    public function isAllowed(){
       return App_Expert_app_Custom_Checkout_Helper::is_checkout_custom_page();
    }
}