<?php
class App_Expert_Peepso_Friends_send_Notification{

    private $_current_integration;
    private $settings_map = [
        'friends_requests' => ['accept_request'],
    ];
    public function __construct(App_Expert_Integration $_current_integration)
    {
        $this->_current_integration = $_current_integration;
        add_action('peepso_friends_requests_after_add', array($this, 'add_notification'), 10, 2);
        add_action('peepso_action_create_notification_after',array($this,'add_push_notification'),99,1);
        add_filter( "ae_send_push_notification_object" , array($this,'add_push_notification_additional_params'), 10, 2);
    }

    public function add_push_notification($id){
        $record   = App_Expert_Peepso_Core_Notification_helper::get_notification($id);
        if($record){
            $settingsList = $this->settings_map;
            if(!in_array($record->not_type,array_keys($this->settings_map))) return;
            $settingNames = $settingsList[$record->not_type];
            $notification_options = get_option('notification_options',[]);
            if(empty($notification_options)||empty($notification_options['peepso_friends_settings']))return;
            $is_option_exist = false;
            foreach ($settingNames as $settingName)
            {
                $is_option_exist = array_key_exists($settingName,$notification_options['peepso_friends_settings']);
                if($is_option_exist) break;
            }
            if(!$is_option_exist) return;
            $user_id = $record->not_user_id;
            if(!$user_id) return;
            App_Expert_Notification_Helper::save_automatic(
                $record->not_message,
                "",
                $record->not_type, $record->not_id ,
                $user_id,[],
                'peepso-core');
            }
    }

    public function add_push_notification_additional_params($_notification,$lang){
        if(in_array($_notification["type"],array_keys($this->settings_map))) {
            $_notification = App_Expert_Peepso_Friends_Notification_helper::get_extra_data((object)$_notification);
            $_notification->title = $_notification->sender["user_fullname"]." ".$_notification->title;
            $_notification = (array)$_notification;
        }
        return $_notification;
    }

    public function add_notification($from_id,$to_id){
        
        $notification_options = get_option('notification_options',[]);
        if(empty($notification_options)||empty($notification_options['peepso_friends_settings']))return;
        $is_option_exist = false;
        $is_option_exist = array_key_exists('receive_request',$notification_options['peepso_friends_settings']);
        if(!$is_option_exist) return;

        $SenderUser  = PeepSoUser::get_instance($from_id);
        $sender = (new App_Expert_User_Serializer($SenderUser))->get();
        $domain = 'app-expert';
        $_langs = App_Expert_Language::get_active_languages();
        $title=[];
        $content=[];
        //prepare segments to send to
        foreach ($_langs as $lang=>$obj){
            App_Expert_Language::switch_lang($lang);
            $title[$lang]   = translate('Friend Rquest'  , $domain);
            $content[$lang] = sprintf(...array_merge([translate('New Friend Request from %s', $domain)], array($sender["user_fullname"])));
        }

        $data=[
            "title" => json_encode($title),
            "content" => json_encode($content),
            "target" => null,
            "segment" => "",
            "attachment_id" => null,
            "type" => 'new_friends_requests',
            "object_id" => null,
            "created_at" => gmdate( 'Y-m-d H:i:s' )
        ];

        App_Expert_Notification_Helper::send_push_to_user($data, $to_id);
    }
}