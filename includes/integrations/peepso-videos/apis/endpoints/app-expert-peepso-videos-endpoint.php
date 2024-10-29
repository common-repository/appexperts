<?php

class App_Expert_Peepso_Videos_Endpoint
{
    public function upload_video(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());

        if(!is_array($_FILES['filedata']['size']))
            $_FILES['filedata']['size']=[$_FILES['filedata']['size']];

        $PeepSoVideosAjax   =PeepSoVideosAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoVideosAjax->upload_video($resp);
        if($resp->success){
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);



    }

    public function get(WP_REST_Request $request){

        $page = $request->get_param('page')??1;
        $sort = $request->get_param('sort')??'desc';

        $limit = $request->get_param('limit')??10;
        $offset = ($page - 1) * $limit;

        if ($page < 1) {
            $offset = 0;
        }
        $owner = $request->get_param('user_id')??get_current_user_id();
        $data = array(
            'owner_id' => $owner,
            'module_id' => 0
        );
        if($request->get_param('group_id')){
            $_GET['group_id'] = (int)$_GET['group_id'];
        }
        $data = apply_filters('ae_peepso_handle_get_video_request', $data, $request);

        $owner   = $data['owner_id'];
        $_GET['module_id'] = $data['module_id'];

        $module_id= $_GET['module_id'];
        $videos_model = new PeepSoVideosModel();
        $media_type = $request->get_param('media_type')??'all';
        $videos = $videos_model->get_user_videos($owner, $media_type, $offset, $limit, $sort, $module_id);

        $videosData = [];

        if(!empty($videos)) {
            foreach ($videos as $key => $video) {
                $videosData[] = (new App_Expert_Video_Serializer($video))->get();
            }
        }

        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $videosData);
    } 

}
