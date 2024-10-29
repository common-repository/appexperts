<?php
class App_Expert_Response{

    public static function success($code,$message,$data){
        return array(
            'code' => $code,
            'message' => $message,
            'data' => array_merge(
                array('status' => 200),
                $data
            )

        );
    }

    public static function fail($code,$message,$data,$status=500){
        return array(
            'code' => $code,
            'message' => $message,
            'data' => array_merge(
                array('status' => $status),
                $data
            )

        );
    }

    public static function wp_list_success(WP_REST_Response $response, string  $code, string  $message)
    {
        $headers = $response->get_headers();
        return self::success($code,$message,[
            'items' => $response->get_data(),
            'current_page' => isset($_GET['page']) ? intval($_GET['page']) : 1,
            'total_count' => isset($headers['X-WP-Total']) ? $headers['X-WP-Total'] : 0,
            'total_pages' => isset($headers['X-WP-TotalPages']) ? $headers['X-WP-TotalPages'] : 0,
            'has_next' => self::is_link_next_or_prev(true, $headers),
            'has_prev' => self::is_link_next_or_prev(false, $headers)
        ]);
    }
    public static function list_success(string  $code, string  $message,$data)
    {
        return self::success($code,$message,[
            'items' => $data,
            'current_page' => isset($_GET['page']) ? intval($_GET['page']) : 1,
            'total_count' =>  0,
            'total_pages' =>  0,
            'has_next' => false,
            'has_prev' => false
        ]);
    }

    public static function wp_rest_success(WP_REST_Response $response, string  $code, string  $message){
        return self::success($code,$message,$response->get_data());
    }
    public static function is_link_next_or_prev(bool $search_for_next, array  $headers)
    {
        if (!isset($headers['Link']) || empty($headers['Link'])) return false;

        $link = $headers['Link'];
        if ($search_for_next) return strpos($link, 'next') !== false;

        return strpos($link, 'prev') !== false;
    }

}