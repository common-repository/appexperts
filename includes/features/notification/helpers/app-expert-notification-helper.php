<?php
class App_Expert_Notification_Helper {

    public static function save(array $title,array $content, $target=null, $segment="", $attachment_id=null,$type="manual",$object_id=null,$user_id=null){
            global $wpdb;
            $data=[
                "title" => json_encode($title),
                "content" => json_encode($content),
                "target" => $target,
                "segment" => json_encode($segment),
                "attachment_id" => $attachment_id,
                "type" => $type,
                "object_id" => $object_id,
                "created_at" => gmdate( 'Y-m-d H:i:s' )
            ];
            $wpdb->insert($wpdb->prefix .NOTIFICATION_TABLE,$data);

            $notification_id = $wpdb->insert_id;
            if(!$notification_id) return [];
            $data['id']= $notification_id;
            if($user_id){
                $wpdb->insert($wpdb->prefix .USER_NOTIFICATION_TABLE,[
                    "user_id" => $user_id,
                    "notification_id" => $notification_id,
                    "is_read" => "0"
                ]);
            }
            $firstKey = array_key_first($title);
            $id=wp_insert_post([
                'post_title'=>"$type Push => title:{$title[$firstKey]}...",
                'post_type'=>'as_notifications',
                'post_status'=>'publish',
                ]);
            add_post_meta($id,'_as_push_notification_id',$notification_id);
            return $data;
        }

    public static function get_condition(WP_User $user){
        global $wpdb;
        $date=$user->user_registered;
        $user_id = $user->ID;
        $currentUserRoles = $user->roles;
        $currentUserRoles = '"%'.implode('%" or like "%',$currentUserRoles).'%"';
        $notification_table = $wpdb->prefix . NOTIFICATION_TABLE;
        $user_notification_table = $wpdb->prefix . USER_NOTIFICATION_TABLE;
        $_condition_str  ="";
        $_conditions = apply_filters('ae_notifications_extra_conditions',[
            "(notification.type ='manual' and notification.segment like {$currentUserRoles})",
            "(notification.type ='manual' and notification.segment = '[]')",
            "(notification.type ='manual' and notification.segment like '%loggedin%')",
            "(notification.type ='user'   and notification_user.id is not null )",
            "(notification.type ='post')",
          ]);

        $_condition_str .=implode(" or ",$_conditions);

        return "from $notification_table as notification
            left join $user_notification_table as notification_user on (notification.id=notification_user.notification_id and notification_user.user_id='{$user_id}')
            where 
                 notification.created_at >= '{$date}'
             and (
              $_condition_str
           )";

    }

    public static function get_unseen_count($user){
        global $wpdb;
        $qCond=self::get_condition($user);
        return (int)$wpdb->get_var("select count(notification.id)
                $qCond
                and (notification_user.is_read !=1 or notification_user.is_read is null )");
    }

    public static function send_manual_push(array $title, array $content, $target=null, $segment=[], $attachment_id=null){
        $_notification = self::save($title,$content,$target,$segment,$attachment_id);
        if($_notification){
            $segments=[];
            $_notification['segment']=json_decode($_notification['segment'],true);
            foreach ($_notification['segment'] as $s){
                $segments[]="'$s' in topics";
            }
            $segments=implode(" || ",$segments);
            self::send_push_segment_by_languages($_notification,$segments);
        }
        return $_notification;
    }
    public static function save_automatic($title_txt,$content_txt,$type,$object_id,$user_id=null,$content_params=[],$domain='app-expert'){
        $_langs = App_Expert_Language::get_active_languages();
        $title=[];
        $content=[];
        //prepare segments to send to
        foreach ($_langs as $lang=>$obj){
            App_Expert_Language::switch_lang($lang);
            $title[$lang]   = translate($title_txt  , $domain);
            $content[$lang] = sprintf(...array_merge([translate($content_txt, $domain)],$content_params));
        }
        $data=self::save($title,$content,null,[],null,$type,$object_id,$user_id);
        $data['segment']=null;
        if($data){
            if($user_id){
                self::send_push_to_user($data,$user_id);
            }else{
                self::send_push_segment_by_languages($data);
            }
        }
        return $data;
    }

    public static function send_push_to_user(array $_notification,$user_id)
    {
        $_user_token   = get_user_meta($user_id,'_as_notification_tokens',true);
        if(!$_user_token) return;

        $_lang             = get_user_meta($user_id,'_ae_user_lang',true);
        if(!$_lang) $_lang = App_Expert_Language::get_user_locale($user_id);

        if(!empty($_notification['attachment_id'])){
            $_notification['attachment']=wp_get_attachment_url($_notification['attachment_id']);
        }

        $_notification['title'] = json_decode($_notification['title'],true);
        $_notification['title'] = $_notification['title'][$_lang];
        $_notification['content'] = json_decode($_notification['content'],true);
        $_notification['content'] = $_notification['content'][$_lang];

        $_notification =  apply_filters("ae_send_push_notification_object",$_notification,$_lang);

        if(!is_array($_user_token))$_user_token=[$_user_token];
        App_Expert_FCM_Helper::send_message($_notification['title'],$_notification['content'],$_user_token,$_notification,'token');
    }

    public static function send_push_segment_by_languages(array $_notification,$segments="")
    {
        if(!empty($_notification['attachment_id'])){
            $_notification['attachment']=wp_get_attachment_url($_notification['attachment_id']);
        }
        $_notification['title'] =json_decode($_notification['title'],true);
        $_notification['content'] =json_decode($_notification['content'],true);
        $_langs = App_Expert_Language::get_active_languages();
        foreach($_langs as $lang=>$obj){
           $id="'$lang' in topics".(!empty($segments)?" && ($segments)":"");
           $_notification =  apply_filters("ae_send_push_notification_object",$_notification,$lang);
           App_Expert_FCM_Helper::send_message($_notification['title'][$lang],$_notification['content'][$lang],$id,$_notification,'topic',$_notification['attachment']);
        }
    }
    public static function send_push_to_users_by_tokens(array $_notification,$user_tokens=[],$silent_notify=null)
    {
        if(!isset($user_tokens) || empty($user_tokens)) return;
        $_notification['attachment']=null;
        if(!empty($_notification['attachment_id'])){
            $_notification['attachment']=wp_get_attachment_url($_notification['attachment_id']);
        }
        $_notification['title'] =json_decode($_notification['title'],true);
        $_notification['content'] =json_decode($_notification['content'],true);
        
        // $_langs = App_Expert_Language::get_active_languages();
        $lang_en='en';
        $title=isset($_notification['title'][$lang_en])?$_notification['title'][$lang_en]:'';
        $content=isset($_notification['content'][$lang_en])?$_notification['content'][$lang_en]:'';
        $_notification =  apply_filters("ae_send_push_notification_object",$_notification,$lang_en);
        App_Expert_FCM_Helper::send_message($title,$content,$user_tokens,$_notification,'token',$_notification['attachment'],$silent_notify);
    }
}
