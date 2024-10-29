<?php 

class App_Expert_Messages_Serializer {

    protected $message;

    public function __construct($message){
        $this->message=$message;
    }
    public function get( ){
        $message_data=array();
        $PeepSoUser		= PeepSoUser::get_instance($this->message->post_author);
        $conv_id=wp_get_post_parent_id($this->message->ID)==0?$this->message->ID:wp_get_post_parent_id($this->message->ID);
        $message_data['conversation_id']=$conv_id;
        $message_data['message_id']=$this->message->ID;
        $message_data['user_id']=$PeepSoUser->get_id();
        $message_data['user_fullname']=$PeepSoUser->get_fullname();
        $message_data['user_avatar']=$PeepSoUser->get_avatar();
        $message_data['message_content']=$this->message->post_content;
        $message_data['message_leave_notic']=false;
        $message_data['message_read_flag']=App_Expert_Peepso_Messages_Messages_Helper::get_message_read_status($this->message->ID,$conv_id);
        if($this->message->post_type == PeepSoMessagesPlugin::CPT_MESSAGE_INLINE_NOTICE && $this->message->post_content =='left')
        {
            $message_data['message_leave_notic']=true;
            $message_data['message_content']=sprintf(__('%s left the conversation', 'msgso'), $PeepSoUser->get_fullname());
        }
        $time_age=App_Expert_Peepso_Messages_Conversations_Helper::post_time_age($this->message->ID);
        $message_data=array_merge($message_data,$time_age);


        if(metadata_exists('post',$this->message->ID,PeepSoMoods::META_POST_MOOD)){
            $post_mood_id = get_post_meta($this->message->ID, PeepSoMoods::META_POST_MOOD,true);
            $post_mood = apply_filters('peepso_moods_mood_value', $post_mood_id);
            $moods = PeepSoMoods::get_instance();
            $name=$moods->moods[$post_mood_id];
            $message_data['message_mood']=[
            'mood_id'=>$post_mood_id,
            'post_mood'=>$post_mood,
            'icon_url'=>APP_EXPERT_URL."includes/integrations/peepso-core/assets/imgs/emojis/{$name}.png"
            ];
        }
        
        
        if(metadata_exists('post',$this->message->ID,'peepso_giphy')){
            $post_giphy = get_post_meta($this->message->ID, 'peepso_giphy');
            $message_data['message_giphy'] = $post_giphy;
            $message_data['message_type'] = 'giphy';
        }

        if(class_exists('PeepSoPhotosModel')){
            $img_ins = new PeepSoPhotosModel();
            $imgs = $img_ins->get_post_photos($this->message->ID);
            if(!empty($imgs))
            {
                $imgs_location=array();
                foreach($imgs as $img)
                {
                    $imgs_location[]=$img->location;
                }
                $message_data['message_photos']=$imgs_location;
                $message_data['message_type'] = 'photo';
            }
        }
        return $message_data;
    }
}