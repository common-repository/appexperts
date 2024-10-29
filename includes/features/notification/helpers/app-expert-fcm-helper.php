<?php
class App_Expert_FCM_Helper {
    public static function set_topics($topic,$userToken,$addFlag=true){
        $server_key=get_option('server_key');
        if (empty($server_key)) return;

        if($addFlag)$curl = "batchAdd";
        else $curl = "batchRemove";

        $baseUrl = "https://iid.googleapis.com/iid/v1:$curl";

        $log=[
            "baseUrl"=>$baseUrl,
            "operation"=>$curl,
            "topic"=>$topic,
            "userToken"=>$userToken
        ];
        $mypush = array(
            "to"=>"/topics/$topic",
            "registration_tokens"=>array_values(is_array($userToken)?$userToken:array($userToken))
        );
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $server_key;

        $myjson = json_encode($mypush);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS,$myjson);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response=curl_exec($ch);
        $log=array_merge($log,["response"=>$response,"json"=>$myjson,"headers"=>$headers]);
        $data= get_option('_as_topic_log',true);
        if(!is_array($data)) $data=[];
        $data[]=$log;
        update_option("_as_topic_log",$data);
    }

    public static function send_message($title, $message, $id = "", $extras = [],$type='topic',$img_url=null,$silent_notify=null)
    {
        $key =get_option('server_key');
        if (empty($key)) return false;

        $topic =$id; // Topic or Token devices here
        if(is_array($id)){
            $topic=array_values($id);
        }
        $url = "https://fcm.googleapis.com/fcm/send";

        $type = $type=="token"?'registration_ids':"condition";
        $notification = array('title' => $title, 'body' => $message, 'sound' => 'default', 'badge' => '1');
        $data = array('extraInfo' => 'From App Expert');
        $data = array_merge($data, $extras);
        if(isset($data['sender']))
            $data['sender']=json_encode($data['sender']);
        if(isset($data['extra_data']))
            $data['extra_data']=json_encode($data['extra_data']);

        $arrayToSend = array(
            $type => $topic,
            'notification' => $notification,
            'priority' => 'high',
            'data' => $data,
            "android"=>[
                "notification"=>[
                    "icon"=>"stock_ticker_update",
                ]
            ]
        );
        if($silent_notify)
        {
            unset($arrayToSend["notification"]);
            $arrayToSend["content_available"]=true;
        }
        if(!empty($img_url))
        {
            $arrayToSend["apns"]=["fcm_options"=> ["image"=>$img_url]];
            $arrayToSend["notification"]["image"]=$img_url;
        }

        $json = json_encode($arrayToSend);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Authorization: key=' . $key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Send the request
        $response = curl_exec($ch);
        //Close request
        $log=[$response,$json,$headers,$arrayToSend];
        if ($response === FALSE) {
            $log[]= 'FCM Send Error: ' . curl_error($ch);
        } else {
            $res_arr = json_decode($response, true);
            $log[]=$res_arr;
            if (isset($res_arr['message_id']) && $res_arr['message_id']) {
                $success = true;
            } else {
                $success = false;
            }
        }
        // $data= get_option('_as_send_log',true);
        // if(!is_array($data)) $data=[];
        // $data[]=$log;
        // update_option("_as_send_log",$data);
        App_Expert_Logger::info("push notification :",["Data"=>$log]); 
        curl_close($ch);
        return $success;
    }

}
