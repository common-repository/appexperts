<?php
class App_Expert_Auth_Request extends App_Expert_Request{
    public function validate()
    {
        $is_valid = App_Expert_Api_jwt_Token_Helper::is_auth($this->request);
        if(is_wp_error($is_valid)) {
            $this->errors[]=$is_valid;
            return false;
        }
        return parent::validate();
    }
    public function getErrors(){
        return $this->errors;
    }
    public function getErrorStatus(){
        return 401;
    }
}