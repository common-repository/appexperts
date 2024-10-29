<?php
class App_Expert_Request{
    /**
     * each rule should return true/false
     */
    protected $request;
    protected $errors=[];
    public function __construct(WP_REST_Request $request){
        $this->request=$request;
    }
    public function rules(){
        return [];
    }
    public function validate()
    {
        $flag = true;
        foreach ($this->rules() as $validation_method){
            $valid = $this->$validation_method();
            if(is_wp_error($valid)){
                $flag = false;
                $this->errors[]= $valid;
            }
        }
        return $flag;
    }
    public function getErrors(){
        return $this->errors;
    }
    public function getErrorStatus(){
        return 400;
    }
}