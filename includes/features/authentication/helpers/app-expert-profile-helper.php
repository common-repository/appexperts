<?php
class App_Expert_Profile_Helper{
    public static function remove_user_meta_data_register(WP_REST_Request $request){
        $params = $request->get_params();
        $data_to_remove = self::get_user_data_to_delete_general();
        return self::remove_data_from_request($request , $data_to_remove , $params);

    }

    public static function remove_user_meta_data_update(WP_REST_Request $request){
        $params = $request->get_params();
        $data_to_remove = self::get_user_data_to_delete_update();
        return self::remove_data_from_request($request , $data_to_remove , $params);

    }

    private static function remove_data_from_request(WP_REST_Request $request , array $data_to_remove , array  $params){
        if (!isset($params) || empty($params) || !isset($data_to_remove) || empty($data_to_remove) )
            return $request;

        foreach ($data_to_remove as $data){
            if (isset($params[$data]))
                unset($params[$data]);
        }

        $request->set_body_params($params);
        return $request;
    }

    private static function get_user_data_to_delete_general(){
        return array(
            'name',
            'url',
            'description',
            'link',
            'locale',
            'nickname',
            'slug',
            'registered_date',
            'roles',
            'capabilities',
            'extra_capabilities',
            'avatar_urls',
            'meta'
        );
    }

    private static function get_user_data_to_delete_update(){
        return array_merge(array(
            'username',
            'password'
        ) , self::get_user_data_to_delete_general());
    }
}