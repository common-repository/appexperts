<?php


/**
 * ======================
 * Author : Marina Wagih.
 * Date :2/2/20.
 * ======================
 */
class App_Expert_Peepso_Core_Response_Helper
{
    const STATUS_CODE_SUCCESS = 200;
    const STATUS_CODE_CREATED = 201;

    const STATUS_CODE_UNAUTHORIZED = 401;
    const STATUS_CODE_NOT_FOUND = 404;
    const STATUS_CODE_BAD_REQUEST = 400;
    const STATUS_CODE_FORBIDDEN = 403;
    const STATUS_CODE_UNPROCESSABLE_ENTITY = 422;

    const STATUS_CODE_SOMETHING_WRONG_HAPPENED = 500;
    public static function response($status, $data)
    {
        $data['statusCode'] = $status;
        $response = new WP_REST_Response($data);
        $response->set_status($status);
        return $response;
    }

    public static function success_response($status, $data = [])
    {
        return self::response($status, [
            "status" => true,
            "data" => $data,
            "error" => []
        ]);
    }

    public static function fail_response($status, $error = [])
    {
//        return self::response($status, [
//            "status" => false,
//            "data" => null,
//            "error" => $error
//        ]);

        return new WP_Error("peepso_api_error",$error[0],['status'=>$status,'errors'=>$error]);
    }
}