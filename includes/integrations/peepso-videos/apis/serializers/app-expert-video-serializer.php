<?php 
/**
* ======================
* Author : Mahmoud Ramadan. 
* Date :6/14/21.    
* ======================
*/
class App_Expert_Video_Serializer {

    protected $video;
    protected $videoKeys=[
        "vid_id", "vid_album_id", "vid_post_id", "vid_acc", "vid_title",
        "vid_album", "vid_description", "vid_thumbnail", "vid_url","vid_size","vid_created",
        "vid_module_id", "act_id", "act_owner_id", "act_external_id","act_module_id","act_access",
        "act_has_replies"
    ];

    public function __construct($video){
        $this->video=$video;
    }
    public function get( ){
        
        $Arr=[];
        foreach($this->videoKeys as $key){
            $Arr[$key]= $this->video->$key;
        }
        $post_type =  get_post_meta($this->video->vid_post_id,'peepso_media_type',true);
        $Arr['type'] = $post_type;

        $Arr['comment_count'] = !empty($this->video->act_id)?(int)App_Expert_Peepso_Video_Activity_Helper::getCommentCount($this->video->vid_post_id):0;
        return $Arr;
    }
}