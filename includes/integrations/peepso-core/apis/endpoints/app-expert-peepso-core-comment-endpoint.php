<?php
class App_Expert_Peepso_Core_Comment_Endpoint{
    public function add_comment(WP_REST_Request $request){
        $_POST['uid']     = get_current_user_id();
        $_POST = array_merge($_POST,$request->get_params());
        $_POST = apply_filters('ae_peepso_handle_add_comment_request',$_POST,$request);


        $PeepSoActivity   = new PeepSoActivity();
        $resp = new PeepSoAjaxResponse();
        $PeepSoActivity->makecomment($resp);

        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('this comment has been added successfully.', 'peepso-core'));
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        }
        else
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->notices);

    }
    private function getCommentManually($ID,$page=1,$limit=5){
        global $wpdb;
        $offset = ($page-1)*$limit;
        $sql = "SELECT act.*,wpp.*"
            . " FROM {$wpdb->prefix}" . PeepSoActivity::TABLE_NAME . " AS act"
            . " LEFT JOIN {$wpdb->posts} AS wpp ON wpp.ID =act.act_external_id "
            . " WHERE act.act_comment_object_id =  %d "
            . " ORDER BY wpp.post_date DESC "
            . " LIMIT $offset,$limit"
        ;
        $sql = $wpdb->prepare($sql, $ID);
        $result =  $wpdb->get_results($sql,ARRAY_A);
        $data=[];
        foreach($result as $comment) {
            $a = (new App_Expert_Comment_Serializer($comment))->get();
            $a['replies'] = $this->getCommentManually($comment['act_external_id'],1,2);
            $data[]=$a;
        }
        return $data;
    }

    public function get_comments(WP_REST_Request $request){
        $act_id=$request->get_param('act_id');
        $page=$request->get_param('page')??1;
        $limit=$request->get_param('limit')??5;
        $activity = new PeepSoActivity();
        $activity = $activity->get_activity_post($act_id);
        $data=$this->getCommentManually($activity->ID,$page,$limit);
        return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$data);
    }

    public function update_comment(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());
        $_POST['no_html'] = 1;
        $_POST['postid'] = $request->get_param('post_id');
        $_POST['post'] = $request->get_param('content');
        $_POST['uid']     = get_current_user_id();

        $_POST = apply_filters('ae_peepso_handle_update_comment_request',$_POST,$request);

        $PeepSoActivity   = new PeepSoActivity();
        $resp = new PeepSoAjaxResponse();
        $PeepSoActivity->savecomment($resp);

        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('Changes saved.', 'peepso-core'));
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        }
        else
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,["you have no permission to update this comment"]);

    }

    public function delete_comment(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());
        $_POST['no_html'] = 1;
        $_POST['postid'] = $request->get_param('post_id');
        $_POST['uid']     = get_current_user_id();

        $PeepSoActivity   = new PeepSoActivity();
        $resp = new PeepSoAjaxResponse();

        $PeepSoActivity->delete($resp);

        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('comment has been deleted successfully.', 'peepso-core'));
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        }
        else
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,["you have no permission to delete this comment"]);

    }

    public function add_like(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $_POST['no_html'] = 1;
        $_POST['act_id'] = $request->get_param('act_id');
        $_POST['uid']     = get_current_user_id();

        $PeepSoActivity   = new PeepSoActivity();
        $resp = new PeepSoAjaxResponse();

        $PeepSoActivity->like($resp);
        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('like action has been added successfully.', 'peepso-core'));
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        }
        else
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,["you have no permission to like this comment"]);

    }


}