<?php
class App_Expert_Firebase_Login_Helper{
    public static function get_user_from_access_token($token)
    {
        $key = get_option('api_key');
        if (empty($key)) return null;
        $url = "https://identitytoolkit.googleapis.com/v1/accounts:lookup?key=$key";


        $arrayToSend = array('idToken' => $token);
        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Send the request
        $response = curl_exec($ch);
        //Close request
        if ($response === FALSE) {
            echo 'FCM Send Error: ' . curl_error($ch);
        }else{
            $res_arr = json_decode($response, true);

            if(isset($res_arr['users']) && $res_arr['users']){
                $user = $res_arr['users'][0];
            }else{
                $user = null;
            }
        }
        curl_close($ch);
        return $user;
    }
}