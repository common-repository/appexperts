<?php 
/**
* ======================
* Author : Mahmoud Ramadan. 
* Date :5/27/21.    
* ======================
*/
class App_Expert_Comment_Serializer {

    protected $activity;
    protected $commentAttributes=[
        "ID", "post_author", "post_content", "post_excerpt", "comment_status",
        "comment_count","post_date",
        "act_id", "act_owner_id", "act_has_replies",
        "act_comment_object_id", "act_comment_module_id"
        
    ];

    public function __construct($activity){
        $this->activity=$activity;
    }
    public function get(){
        $Arr=[];
        foreach($this->commentAttributes as $key){
            $Arr[$key]= $this->activity[$key];
        }
        $comment_giphy = get_post_meta($this->activity["ID"], 'peepso_giphy', TRUE);
        if($comment_giphy){
            $Arr['giphy'] = $comment_giphy;
        }
        $PeepSoUser  = PeepSoUser::get_instance($this->activity["post_author"]);
        $Arr['author_data']=(new App_Expert_User_Serializer($PeepSoUser))->get();
        $Arr['author_id']=$this->activity['post_author'];
        $output_array=[];
        $mention_matches = preg_match_all('/@peepso_user_(\d+)(?:\(([^\)]+)\))?/', $this->activity["post_content"], $output_array);
        if($mention_matches){
            $Arr['mention_data']=[];
            foreach ($output_array[1] as $key=>$uid){
                $PeepSoUser  = PeepSoUser::get_instance($uid);
                $Arr['mention_data'][$output_array[2][$key]]=(new App_Expert_User_Serializer($PeepSoUser))->get();

            }
        }
        // get number of likes for comment
        $PeepSoLike = PeepSoLike::get_instance();
        $like_count = $PeepSoLike->get_like_count($this->activity["ID"]);
        $Arr['likes'] = $like_count;

        // check if current user like comment or not
        $like = PeepSoLike::get_instance();
        $user_liked = $like->user_liked($this->activity["ID"], $module_id = PeepSoActivity::MODULE_ID, get_current_user_id());
        $Arr['current_user_liked'] = $user_liked;
        $Arr['reply_count']=$this->getReplyCount();
        return apply_filters("ae_peepso_comment_object",$Arr,$this->activity);
    }

    private function getReplyCount(){
        global $wpdb;
        $sql = "SELECT count(*)"
            . " FROM `{$wpdb->prefix}" . PeepSoActivity::TABLE_NAME . "`"
            . " WHERE `act_comment_object_id` =  %d ";

        $sql = $wpdb->prepare($sql, $this->activity['ID']);

        return $wpdb->get_var($sql);
    }
}