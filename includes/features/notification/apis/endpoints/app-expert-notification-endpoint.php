<?php

class App_Expert_Notification_Endpoint
{
    public function get_object_data($notificationObj){
        $local  = App_Expert_Language::get_user_locale(get_current_user_id());
        $title=json_decode($notificationObj->title,true);
        $firstKey = array_key_first($title);
        $notificationObj->title=isset($title[$local])?$title[$local]:(isset($title[$firstKey])?$title[$firstKey]:"");

        $content=json_decode($notificationObj->content,true);
        $firstKey = array_key_first($content);
        $notificationObj->content=isset($content[$local])?$content[$local]:(isset($content[$firstKey])?$content[$firstKey]:"");

        $notificationObj->created_at = date('c', strtotime($notificationObj->created_at));
        $notificationObj->segment=null;
        $notificationObj->attachment=null;
        if(!empty($notificationObj->attachment_id)){
            $notificationObj->attachment=wp_get_attachment_url($notificationObj->attachment_id);
        }
        switch ($notificationObj->type) {
            case "post":
                $notificationObj->object_name = get_the_title($notificationObj->object_id);
                break;
            case "user":
                $user = get_user_by( 'id', $notificationObj->object_id );
                $user_name = $user->first_name . ' ' . $user->last_name;
                $notificationObj->object_name = $user_name;
                break;
            case "manual":
                $notificationObj->object_name = '';
                break;
        }
        
        $notificationObj = apply_filters('ae_notification_object', $notificationObj);
        
        return $notificationObj;
    }

    public function get_items($request)
    {
        global $wpdb;
        $response=new WP_REST_Response();
        $user = wp_get_current_user();
        $limit = $request->get_param('per_page')??5;
        $page  = $request->has_param('page')?$request->get_param('page')-1:0;
        $skip = $page * $limit;
        $qCond=App_Expert_Notification_Helper::get_condition($user);
        $q="select notification.*, IFNULL(notification_user.is_read, 0) 'is_read'
           $qCond
           order by id desc
           LIMIT {$limit} OFFSET {$skip}";
        $qCount="select count(notification.id) $qCond ";
        $data=$wpdb->get_results($q);
        $items=[];
        foreach ($data as $obj){
            $items[]=$this->get_object_data($obj);
        }
        $response->set_data($items);

        //todo:find a better way
        $res= App_Expert_Response::wp_list_success(
            $response,
            'app_expert_notification_retrieved',
            'retrieved all notification'
        );

        $res['data']['unseenCount'] = App_Expert_Notification_Helper::get_unseen_count($user);
        $res['data']['total_count'] = (int)$wpdb->get_var($qCount);
        $res['data']['total_pages'] = ceil((int)$res['data']['total_count']/(int)$limit);
        $res['data']['has_next'] = $res['data']['current_page']<$res['data']['total_pages'];
        $res['data']['has_prev'] = $res['data']['current_page']>1;
        return $res;
    }

    public function mark_as_seen(WP_REST_Request $request){
        global $wpdb;
        $user_id = get_current_user_id();
        $user = wp_get_current_user();
        $notification_id=$request->get_param('notification_id');

        $qCond=App_Expert_Notification_Helper::get_condition($user);
        $q="select notification.*, IFNULL(notification_user.is_read, 0) 'is_read'
           $qCond
           and notification.id = $notification_id
           order by id desc
           LIMIT 1 OFFSET 0";
        $data=$wpdb->get_row($q);
        if(!$data){
            return new WP_Error("app_expert_notification_error",__("you are not allowed"),["status"=>401]);
        }

        $user_notification_table = $wpdb->prefix . USER_NOTIFICATION_TABLE;

        $id=$wpdb->get_var("select id 
                              from {$user_notification_table}
                              where user_id = '{$user_id}'
                              and notification_id = '{$notification_id}'");
        if(!$id){
            $wpdb->insert($user_notification_table,[
                "user_id" => $user_id,
                "notification_id" => $notification_id,
                "is_read" => "1"
            ]);
        }else{
            $wpdb->update($user_notification_table,[
                "user_id" => $user_id,
                "notification_id" => $notification_id,
                "is_read" => "1"
            ],[
                "user_id" => $user_id,
                "notification_id" => $notification_id
            ]);
        }
        do_action('ae_notification_after_mark_as_seen',$data);
        return App_Expert_Response::success(
            'app_expert_notification_updated',
            'Notification updated',
            [
                'unseenCount' => App_Expert_Notification_Helper::get_unseen_count($user)
            ]
        );
    }

}