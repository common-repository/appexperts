<?php
/**
 * ==================================
 * Author : marina
 * Date :5/25/21.
 * ==================================
 **/
require_once(APP_EXPERT_PATH."includes/features/authentication/apis/requests/app-expert-auth-request.php");

class App_Expert_Add_Post_Request  extends App_Expert_Auth_Request {

    public function rules(){
        return [
            'type'       => 'check_type',
            'content'    => 'check_content',
            'files'      => 'check_files',
            'fileName'   => 'check_file_name',
            'url'        => 'check_url',
        ];
    }

    public function check_type(){
        $val = $this->request->get_param("type");
        //todo:add_apply filter
        $types = array('activity', 'audio', 'video', 'photo', 'files', 'post_backgrounds', 'giphy', 'poll');
        if (!in_array($val, $types)) {
            return $this->get_validation_error("invalid_type", "type", __("Invalid parameter(s): type", "app-expert"), __("type should be activity, video, audio, photo , post_backgrounds or files", "app-expert"));
        }
        return true;
    }

    public function check_content(){
        $val = $this->request->get_param("content");
        $type = $this->request->get_param("type");
        if ($type === 'activity' && empty($val)) {
            return $this->get_validation_error("empty_content", "content", __("Invalid parameter(s): content", "app-expert"), __("content should not be empty once type of post is activity", "app-expert"));
        }
        return true;
    }

    public function check_files(){
        $val = $this->request->get_param("files");
        $type = $this->request->get_param("type");
        if ($type === 'photo' && empty($val)) {
            return $this->get_validation_error("empty_files", "files", __("Invalid parameter(s): files", "app-expert"), __("files should not be empty once type of post is photo", "app-expert"));
        }
        return true;
    }

    public function check_file_name(){
        $val = $this->request->get_param("fileName");
        $type = $this->request->get_param("type");
        if ($type === 'files' && empty($val)) {
            return $this->get_validation_error("empty_file_name", "fileName", __("Invalid parameter(s): fileName", "app-expert"), __("fileName should not be empty once type of post is files", "app-expert"));
        }
        return true;
    }

    public function check_url(){
        $val = $this->request->get_param("url");
        $type = $this->request->get_param("type");
        $video = $this->request->get_param("video");
        $audio = $this->request->get_param("audio");

        if (($type === 'video' || $type === 'audio') && empty($val) && empty($video) && empty($audio)) {
            return $this->get_validation_error("empty_url", "url", __("Invalid parameter(s): url", "app-expert"), __("url should not be empty once type of post is video or audio", "app-expert"));
        }
        return true;
    }

    private function get_validation_error($key, $field, $message, $error_message)
    {
        return new WP_Error(
            $key,
            $message,
            array(
                'status' => 400,
                'params' => array(
                    $field => $error_message
                )
            )
        );
    }
}