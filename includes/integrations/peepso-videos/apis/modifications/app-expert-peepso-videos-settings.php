<?php
//ae_settings_data
class App_Expert_Peepso_Videos_Settings{
    public function __construct() {
        add_filter( "ae_settings_data", array($this,'add_additional_params_to_settings'), 10, 1 );
    }
    public function add_additional_params_to_settings($data){

        $video_settings = array();
        $video_settings['videos_enabled'] = (string)PeepSo::get_option('videos_video_master_switch', 1);
        $video_settings['videos_play_inline'] =  (string)PeepSo::get_option('videos_play_inline', 0);
        $video_settings['videos_upload_enable'] =  (string)PeepSo::get_option('videos_upload_enable', 0);
        $video_settings['videos_max_upload_size'] =  (string)PeepSo::get_option('videos_max_upload_size', 20);
        $video_settings['videos_allowed_user_space'] =  (string)PeepSo::get_option('videos_allowed_user_space', 0);
        $video_settings['videos_conversion_mode'] =  (string)PeepSo::get_option('videos_conversion_mode', 'no');
        $video_settings['videos_allowed_extensions'] =  PeepSoVideosUpload::no_conversion_mode_filetypes();

        $audio_settings = array();
        $audio_settings['videos_audio_enabled'] =  (string)PeepSo::get_option('videos_audio_master_switch', 1);
        $audio_settings['videos_audio_upload_enable'] =  (string)PeepSo::get_option('videos_audio_enable', 0);
        $audio_settings['videos_audio_max_upload_size'] =  (string)PeepSo::get_option('videos_audio_max_upload_size', 20);
        $audio_settings['videos_audio_allowed_user_space'] =  (string)PeepSo::get_option('videos_audio_allowed_user_space', 0);
        $audio_settings['videos_audio_lastfm'] =  (string)PeepSo::get_option('videos_audio_lastfm', 0);
        $audio_settings['videos_audio_lastfm_api_key'] =  (string)PeepSo::get_option('videos_audio_lastfm_api_key');

        $data['peepso']['video'] = $video_settings;
        $data['peepso']['audio'] = $audio_settings;

        return $data;
    }

}
new App_Expert_Peepso_Videos_Settings();