<?php

class App_Expert_Peepso_Videos_Post {
    public function __construct(){
        add_filter("ae_peepso_post_object",array($this,'add_user_edit'),10,2);
    }
    public function add_user_edit($result,$peepso_post){
        $post_type =  get_post_meta($peepso_post['ID'],'peepso_media_type',true);

        if(in_array($post_type,['peepso-video','peepso-audio'])){
            $video_ins = PeepSoVideos::get_instance();
            $video_data=$video_ins->get_post_video($peepso_post['ID']);
            if(!empty($video_data)){
                switch ($post_type){
                    case 'peepso-video':
                        $result['type']='video';
                        $result['video_data']=$video_data[0];
                        break;
                    case 'peepso-audio':
                        $result['type']='audio';
                        $result['audio_data']=$video_data[0];
                        break;
                }
            }
        }

        return $result;
    }
}
new App_Expert_Peepso_Videos_Post();
