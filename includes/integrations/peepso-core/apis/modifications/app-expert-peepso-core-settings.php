<?php
//ae_settings_data
class App_Expert_Peepso_Core_Settings{
    public function __construct() {
        add_filter( "ae_settings_data", array($this,'add_additional_params_to_settings'), 10, 1 );
    }
    public function add_additional_params_to_settings($data){
        // Get all plugins
        $settings=$this->get_settings();
        $data["peepso"] = $settings;
        return $data;
    }
    public function get_settings(){
        //Posts Options
        $data=[];
        $PeepSoReactionsModel = new PeepSoReactionsModel();
        $reactions = $PeepSoReactionsModel->reactions;
        foreach ($reactions as $reaction) {
            $reaction->icon_url = str_replace('//','https://', $reaction->icon_url);
        }

        $data['reactions'] = $reactions;    

        $data['privacy'] = $this->getPrivacyData();
        $data['hashtags'] = $this->get_hashtag_settings();
        $data['post_backgrounds'] = $this->get_post_background_settings();
        $data['giphy'] = $this->get_post_giphy_settings();
        $data['polls'] = $this->get_post_polls_settings();

        $moods = PeepSoMoods::get_instance();
        $moodsData=[];
        foreach ($moods->moods as $mood_id=>$name){
            $moodsData[]=[
                "id"=>$mood_id,
                "name"=>$name,
                //todo: add filter
                "icon"=> APP_EXPERT_URL."includes/integrations/peepso-core/assets/imgs/emojis/{$name}.png"
            ];
        }
        $data['moods'] = $moodsData;
        // Get all plugins
        include_once( 'wp-admin/includes/plugin.php' );
        $all_plugins = get_plugins();
        // Get active plugins
        $plugins =[];
        // Assemble array of name, version, and whether plugin is active (boolean)
        foreach ( $all_plugins as $key => $value ) {
            if(strpos($key, 'peepso') === 0){
                $plugins[] = $value['Name'];
            }

        }
        $data['plugins'] = $plugins;
        $reasons = str_replace("\r", '', PeepSo::get_option('site_reporting_types', __('Spam', 'peepso-core')));
        $reasons = explode("\n", $reasons);
        $reasons = apply_filters('peepso_activity_report_reasons', $reasons);
        $reasons[] = "Other";
        $reasons_translated = [];
        foreach ($reasons as $reason){
            $reason = esc_attr($reason);
            $reason = stripslashes($reason);
            $reasons_translated[] =[
                "value"=>$reason,
                "label"=>__($reason, 'peepso-core')
            ];
        }
        $data['site_reporting'] = (string)PeepSo::get_option('site_reporting_enable');

        $data['reporting_reasons'] = $reasons_translated;
        $data['location'] = [
            'location_enable'=>(string)PeepSo::get_option('location_enable', 0),
            'location_gmap_api_key'=>PeepSo::get_option('location_gmap_api_key',''),
        ];
        $data['mentions']=[
            'tags_enable'=>PeepSo::get_option('tags_enable', 0),
            'mentions_auto_on_comment_reply'=>PeepSo::get_option('mentions_auto_on_comment_reply',0),
        ];

        $PeepSoUser = PeepSoUser::get_instance(0);
        $profile_fields = new PeepSoProfileFields($PeepSoUser);
        $fields = $profile_fields->load_fields();

        $genders = array();
        if(isset($fields['peepso_user_field_gender'])) {
            $genders_data = $fields['peepso_user_field_gender']->meta->select_options;
            $genders[] =[
                "value"=>"",
                "label"=>__('Any', 'peepso-core')
            ];
            foreach ($genders_data as $key=>$value){
                $genders[] =[
                    "value"=>$key,
                    "label"=>__($value, 'peepso-core')
                ];
            }
        }


        $sort_options=[
            [
                "value"=>"",
                "label"=>__('Alphabetical', 'peepso-core')
            ],
            [
                "value"=>"peepso_last_activity",
                "order"=>'asc',
                "label"=>__('Recently online', 'peepso-core')
            ],
            [
                "value"=>"registered",
                "order"=>'desc',
                "label"=>__('Latest members', 'peepso-core')
            ],
        ];
        if (PeepSo::get_option('site_likes_profile', TRUE)){
            $sort_options[]= [
                    "value"=>"most_liked",
                    "order"=>'desc',
                    "label"=>__('Most liked', 'peepso-core')
                ];
            $sort_options[]= [
                    "value"=>"most_followers",
                    "order"=>'desc',
                    "label"=>__('Most followers', 'peepso-core')
                ];
        }

        $data['member_filters']=[
            'genders' =>$genders,
            'sort'=>[
                'default_sort' => PeepSo::get_option('site_memberspage_default_sorting',''),
                'options' => $sort_options
            ],
            'following'=>[
                [
                    "value"=>-1,
                    "label"=>__('All members', 'peepso-core')
                ],
                [
                    "value"=>1,
                    "label"=>__('Members I follow', 'peepso-core')
                ],
                [
                    "value"=>0,
                    "label"=>__('Members I don\'t follow', 'peepso-core')
                ],
            ]

        ];
        return $data;
    }
    private function getPrivacyData(){
        $privacy = PeepSoPrivacy::get_instance();
        $privacyData = apply_filters('peepso_postbox_access_settings', $privacy->get_access_settings());
        $privacy_settings = [];
        foreach ($privacyData as $key=>$values){
            $values['id']= $key;
            //todo:add filter
            $values['icon']= APP_EXPERT_URL."includes/integrations/peepso-core/assets/imgs/privacy-icons/png/icon-$key.png";
            $privacy_settings[]= $values;
        }
        // admin defined default privacy
        $user_default_privacy = PeepSo::get_option('activity_privacy_default', PeepSo::ACCESS_PUBLIC);

        $user_last_used_privacy = PeepSo::get_last_used_privacy(get_current_user_id());
        if ($user_last_used_privacy) {
            $user_default_privacy = $user_last_used_privacy;
        }
        if(!isset($privacyData[$user_default_privacy])) $user_default_privacy=  array_key_first($privacyData);

        return [
            'values'=>$privacy_settings,
            'default'=>$user_default_privacy.""];

    }

    private function get_hashtag_settings (){
        $settings = array();
        $settings['hashtags_enable'] = (string)PeepSo::get_option('hashtags_enable', 1);
        $settings['hashtags_post_count_interval'] =  (string)PeepSo::get_option('hashtags_post_count_interval', 60); 
        $settings['hashtags_post_count_batch_size'] =  (string)PeepSo::get_option('hashtags_post_count_batch_size', 5); 
        $settings['hashtags_delete_empty'] =  (string)PeepSo::get_option('hashtags_delete_empty', 1); 
        $settings['hashtags_everything'] =  (string)PeepSo::get_option('hashtags_everything', 0); 
        $settings['hashtags_min_length'] =  (string)PeepSo::get_option('hashtags_min_length', 3); 
        $settings['hashtags_max_length'] =  (string)PeepSo::get_option('hashtags_max_length', 16); 
        $settings['hashtags_must_start_with_letter'] =  (string)PeepSo::get_option('hashtags_must_start_with_letter', 0); 
        $settings['hashtags_rebuild'] =  (string)PeepSo::get_option('hashtags_rebuild', 0); 
        return $settings;
    }

    private function get_post_background_settings (){
        $settings = array();
        $settings['post_backgrounds_enable'] =  (string)PeepSo::get_option('post_backgrounds_enable', 0);
        $settings['post_backgrounds_max_length'] =  (string)PeepSo::get_option('post_backgrounds_max_length',150); 
        $settings['post_backgrounds_max_linebreaks'] =  (string)PeepSo::get_option('post_backgrounds_max_linebreaks', 0); 
        $PeepSoPostBackgroundsModel = new PeepSoPostBackgroundsModel(FALSE);
        $backgrounds = $PeepSoPostBackgroundsModel->post_backgrounds;
        foreach ($backgrounds as $background){

            $text_color = $background->content->text_color;
            if(strpos($text_color, 'rgba') === 0){
                $background->content->text_color = $this->change_color_rgba_to_hex($text_color);
            }

            $background_color = $background->content->background_color;
            if(strpos($background_color, 'rgba') === 0){
                $background->content->background_color = $this->change_color_rgba_to_hex($background_color);
            }

            $text_shadow_color = $background->content->text_shadow_color;
            if(strpos($text_shadow_color, 'rgba') === 0){
                $background->content->text_shadow_color = $this->change_color_rgba_to_hex($text_shadow_color);
            }
        }

        $settings['backgrounds'] = $PeepSoPostBackgroundsModel->post_backgrounds;
        return $settings;
    }

    private function get_post_giphy_settings (){

        $settings = array();
        $settings['giphy_api_key'] =  (string)PeepSo::get_option('giphy_api_key');
        $settings['giphy_display_limit'] =  (string)PeepSo::get_option('giphy_display_limit',25); 
        $settings['giphy_rating'] =  (string)PeepSo::get_option('giphy_rating', ''); 
        $settings['giphy_posts_enable'] =  (string)PeepSo::get_option('giphy_posts_enable', 1); 
        $settings['giphy_rendition_posts'] =  (string)PeepSo::get_option('giphy_rendition_posts', 'original'); 
        $settings['giphy_comments_enable'] =  (string)PeepSo::get_option('giphy_comments_enable', 1); 
        $settings['giphy_rendition_comments'] =  (string)PeepSo::get_option('giphy_rendition_comments', 'fixed_width'); 
        return $settings;
    }

    private function change_color_rgba_to_hex ($color){
        $color = str_replace( ' ', '', $color );
        sscanf( $color, 'rgba(%d,%d,%d,%f)', $red, $green, $blue, $alpha );

        $output = "#";
        $output.= dechex((1 - $alpha) * $red + $alpha * $red);
        $output.= dechex((1 - $alpha) * $green + $alpha * $green);
        $output.= dechex((1 - $alpha) * $blue + $alpha * $blue);
        return $output;
    }

    private function get_post_polls_settings (){
        
        $settings = array();
        $settings['polls_enable'] =  (string)PeepSo::get_option('polls_enable', 1);
        $settings['polls_multiselect'] =  (string)PeepSo::get_option('polls_multiselect',1); 
        $settings['polls_changevote'] =  (string)PeepSo::get_option('polls_changevote', 0); 
        $settings['polls_show_result_before_vote'] =  (string)PeepSo::get_option('polls_show_result_before_vote', 0); 
        $settings['polls_sort_result_by_votes'] =  (string)PeepSo::get_option('polls_sort_result_by_votes', 0); 
        $settings['polls_group'] =  class_exists('PeepSoGroupsPlugin') &&PeepSo::get_option('polls_group')?'1':'0'; 
        return $settings;
    }
}
new App_Expert_Peepso_Core_Settings();