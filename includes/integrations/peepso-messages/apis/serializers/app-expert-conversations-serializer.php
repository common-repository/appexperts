<?php 

class App_Expert_Conversations_Serializer {

    protected $message;

    public function __construct($message){
        $this->message=$message;
    }
    public function get( ){

        $args=array('post_author'=>$this->message->post_author,'post_id'=>$this->message->ID);
        $data=[];
        $data['msg_parent_id']= $this->message->mrec_parent_id;
        $data['conv_viewed']= $this->message->mrec_viewed;
    
        $avatars =  App_Expert_Peepso_Messages_Conversations_Helper::get_message_avatar($args);
        $data['avatars'] = $avatars;

        $data['last_author_id']= $this->message->post_author;
        
        $data['message_content']=App_Expert_Peepso_Messages_Conversations_Helper::get_conversation_title($this->message);

        $time_age=App_Expert_Peepso_Messages_Conversations_Helper::post_time_age($this->message->ID);
        $data=array_merge($data,$time_age);
        return $data;
    }
}