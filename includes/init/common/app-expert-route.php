<?php
class App_Expert_Route{
    public static function get($name_space,$route,$action,$args,$validatior=null,$permission_callback='__return_true'){
        self::register(WP_REST_Server::READABLE,$name_space,$route,$action,$args,$validatior,$permission_callback);
    }
    public static function post($name_space,$route,$action,$args,$validatior=null,$permission_callback='__return_true'){
        self::register(WP_REST_Server::CREATABLE,$name_space,$route,$action,$args,$validatior,$permission_callback);
    }
    public static function put($name_space,$route,$action,$args,$validatior=null,$permission_callback='__return_true'){
        self::register(WP_REST_Server::EDITABLE,$name_space,$route,$action,$args,$validatior,$permission_callback);
    }
    public static function delete($name_space,$route,$action,$args,$validatior=null,$permission_callback='__return_true'){
        self::register(WP_REST_Server::DELETABLE,$name_space,$route,$action,$args,$validatior,$permission_callback);
    }
    private static function register($method, $name_space, $route, $action, $args, $validator,$permission_callback){
        register_rest_route($name_space,$route, array(
            'methods'             => $method,
            'callback'            => array("App_Expert_Route","add"),
            'permission_callback' => $permission_callback,
            'args'                => $args,
            'action'              => $action,
            'validator'           => $validator,
        ));
    }
    public static function add(WP_REST_Request $request){

        $attr=$request->get_attributes();
        if(empty($attr['action'])) return App_Expert_Response::fail("invalid_action","no_action_added",[]);

        $callback=explode("@",$attr["action"]);
        if(count($callback)<1) return App_Expert_Response::fail("invalid_action","invalid_action_format",[]);
        $call_back_class=$callback[0];
        $call_back_method=$callback[1];
        $validator= $attr["validator"] ?? "App_Expert_Request";
        try{

            $validation_class=new $validator($request);
            $logs_data=[
                "request"=>$request->get_params(),
                "request_files"=>$request->get_file_params(),
                "request_headers"=>$request->get_headers(),
                "request_"=>$request->get_content_type(),
               ];
            if($validation_class->validate()){
                $response= (new $call_back_class())->$call_back_method($request);
                $logs_data["response"]=$response;
                App_Expert_Logger::info("Api Call {$request->get_route()} : success",$logs_data);
                return $response;
            }
            $error = $validation_class->getErrors();
            $logs_data["errors"]=$error;
            App_Expert_Logger::error("Api Call {$request->get_route()} : Validation Errors",$logs_data);
            return count($error)?$error[0]:new WP_Error("something_went_wrong","error from App_Expert_Routes:add",[]);
        }catch (Exception $e){
            $logs_data["exception"]=$e->getMessage();
            App_Expert_Logger::fatal("Api Call {$request->get_route()} : Exception",$logs_data);
            return App_Expert_Response::fail("something_went_wrong",$e->getMessage(),[]);
        }
    }

}