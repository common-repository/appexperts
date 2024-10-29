<?php



/**
* ======================
* Author : Mahmoud Ramadan. 
* Date :5/20/21.    
* ======================
*/

class App_Expert_Post_Serializer {

    protected $post;
    protected $allowedValues=[
        "ID", "post_author",
        "post_date", "post_content",
        "post_type","act_id", "act_access",
        "act_module_id"
    ];

    public function __construct($post){
        $this->post=$post;
    }
    public function get(){
        $arr=[];
        foreach($this->allowedValues as $key){
            $arr[$key]= $this->post[$key];
        }
        $post_background = get_post_meta($this->post['ID'], 'peepso_post_background', TRUE);
        if($post_background){
            $arr['background'] = json_decode($post_background);
            $arr['type'] = 'post_backgrounds';
        }
        $post_giphy = get_post_meta($this->post['ID'], 'peepso_giphy', TRUE);
        if($post_giphy){
            $arr['giphy'] = $post_giphy;
            $arr['type'] = 'giphy';
        }
        $post_poll_options = get_post_meta($this->post['ID'], 'select_options', true);
        if($post_poll_options){
            $arr['poll_options'] = unserialize($post_poll_options);
            $arr['type'] = 'poll';
            $polls_model = new PeepSoPollsModel();
		    $user_polls = $polls_model->get_user_polls(get_current_user_id(), $this->post['ID']);
            $arr['user_polls'] = $user_polls?$user_polls:null;
        }
        $total_user_poll = get_post_meta($this->post['ID'], 'total_user_poll', true);
        if($total_user_poll >= 0){
            $arr['total_user_poll'] = (int)$total_user_poll;
        }
        $max_answers = get_post_meta($this->post['ID'], 'max_answers', true);
        if($max_answers >= 0){
            $arr['max_answers'] = (string)$max_answers;
        }
        //======== temporary fix date ===========
        //$arr['post_date'] = wp_date('Y-m-d h:i:s',strtotime($arr['post_date']));
        //======== temporary fix date ===========
        $PeepSoUser  = PeepSoUser::get_instance($this->post["post_author"]);
        $arr['author_data']=(new App_Expert_User_Serializer($PeepSoUser))->get();
        $output_array=[];
        $mention_matches = preg_match_all('/@peepso_user_(\d+)(?:\(([^\)]+)\))?/', $this->post["post_content"], $output_array);
        if($mention_matches){
            $arr['mention_data']=[];
            foreach ($output_array[1] as $key=>$uid){
                $PeepSoUser  = PeepSoUser::get_instance($uid);
                $arr['mention_data'][$output_array[2][$key]]=(new App_Expert_User_Serializer($PeepSoUser))->get();

            }
        }
        $post_mood_id = get_post_meta($this->post['ID'], PeepSoMoods::META_POST_MOOD, TRUE);
        if($post_mood_id){
            $post_mood = apply_filters('peepso_moods_mood_value', $post_mood_id);
            $arr['mood']=[
              'mood_id'=>$post_mood_id,
              'post_mood'=>$post_mood
            ];
        }
        $arr['comment_count'] = App_Expert_Peepso_Core_Activity_Helper::getCommentCount($this->post['ID']);

        $arr['reactions']=App_Expert_Peepso_Core_Activity_Helper::getReactionCount($this->post['act_id']);

        $general_count = PeepSoActivityRanking::get_view_count($this->post['act_id']);
        $unique_count = PeepSoActivityRanking::get_unique_view_count($this->post['act_id']);
        $arr['views']=[
            "general_count"=>$general_count,
            'unique_count'=>$unique_count
        ];
        $this->post['pinned']     = get_post_meta($this->post['ID'],'peepso_pinned'    , TRUE);
        $this->post['pinned_by']  = get_post_meta($this->post['ID'],'peepso_pinned_by' , TRUE);

        $arr['pinned']    = !!$this->post['pinned'];
        $arr['pin_date']  = "";
        $arr['pinned_by'] = (int) $this->post['pinned_by'];
        if($arr['pinned']){
            $pinned_date = intval($this->post['pinned']) + 3600 * PeepSoUser::get_gmt_offset(get_current_user_id());
            $pinned_on_label = sprintf(__('%s at %s', 'peepso-core'), date_i18n(get_option('date_format'), $pinned_date), date_i18n(get_option('time_format'), $pinned_date));
            $arr['pin_date']  = $pinned_on_label;
        }
        $arr['get_current_user_id']  = get_current_user_id();
        $arr['can_pin']   = App_Expert_Peepso_Core_Allowed_Plugins_Helper::can_pin(intval($this->post['ID']));
        //======== location data ===========
        $arr['location'] = get_post_meta($this->post['ID'],"peepso_location",true);
        if(empty($arr['location'])) $arr['location']=null;
        //======== location data ===========
        return apply_filters("ae_peepso_post_object",$arr, $this->post);
    }

}