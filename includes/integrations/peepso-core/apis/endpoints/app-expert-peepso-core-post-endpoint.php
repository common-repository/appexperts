<?php

class App_Expert_Peepso_Core_Post_Endpoint {
    public function add(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $_POST['id'] = get_current_user_id();
        $_POST['uid']= $request->get_param('profile_id')??0;
        if($request->get_param('group_id')){
            $_POST['group_id'] = (int)$_POST['group_id'];
        }
        $result = apply_filters('ae_peepso_handle_add_post_request', $_POST, $request);
        if(is_wp_error($result)){
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, $result->get_error_messages());
        }
        $_POST = $result;
        $PeepSoPostboxAjax = PeepSoPostbox::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoPostboxAjax->post($resp);
        if($resp->success){
            $resp->set('msg',__('Post added.', 'peepso-core'));
            unset($resp->data['html']);
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);
    }

    public function get(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $_POST['no_html'] = 1;
        $_POST['user_id'] = $request->get_param('profile_id')??0;
        $_POST['uid']     = get_current_user_id();
        $_POST['page']    = isset($_POST['page'])?(int)$_POST['page']:1;
        $_POST['pinned']  = ($_POST['page']<2)?1:0;
        if($request->get_param('group_id')){
            $_POST['group_id'] = (int)$_POST['group_id'];
        }
        $_POST = apply_filters('ae_peepso_handle_get_post_request', $_POST,$request);

        $PeepSoActivity   = new PeepSoActivity();
        $resp = new PeepSoAjaxResponse();
        $PeepSoActivity->show_posts_per_page($resp);
        $data =[];
        foreach ($resp->data["no_html_data"] as $post){
            $data[]= (new App_Expert_Post_Serializer($post))->get();
        }

        return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,  $data);

    }

    public function get_one(WP_REST_Request $request){
        $act_id = $request->get_param('id');
        $PeepSoActivity   = new PeepSoActivity();
        $act_post = $PeepSoActivity->get_activity_post($act_id);
        if($act_post){
            $data= (new App_Expert_Post_Serializer((array)$act_post))->get();
            return  App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $data);

        }
        return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_NOT_FOUND,["Activity not found"]);

    }

    public function edit(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $_POST['uid'] = get_current_user_id();
        $_POST['_wpnonce'] = wp_create_nonce('peepso-nonce');

        $ajax = new PeepSoActivity();
        $resp=new PeepSoAjaxResponse();
        $ajax->savepost($resp);
        if($request->has_param('acc'))
        {
            $ajax->change_post_privacy($resp);
        }
        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('Changes saved.', 'peepso-core'));
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,  $resp->data);
        }
        else
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);

    }

    public function delete(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());
        $_POST['no_html'] = 1;
        $_POST['postid'] = $request->get_param('post_id');
        $_POST['uid']     = get_current_user_id();

        $PeepSoActivity   = new PeepSoActivity();
        $resp = new PeepSoAjaxResponse();

        $PeepSoActivity->delete($resp);

        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('post has been deleted successfully.', 'peepso-core'));
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,  $resp->data);
        }
        else
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,["you have no permission to delete this post"]);


    }

    public function pin(WP_REST_Request $request){

        $post_id = $request->get_param('post_id');
        $pin_status = $request->get_param('pin_status');
        $user = PeepSoUser::get_instance(get_current_user_id());
        $user_role = $user->peepso_user['usr_role'];

        $PeepSoActivity   = new PeepSoActivity();
        $post = $PeepSoActivity->get_post($post_id);


        if(!App_Expert_Peepso_Core_Allowed_Plugins_Helper::can_pin((int)$post_id)){
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, ["you have no permission to pin this post"]);
        }
        
        if(!isset($post->posts[0]->ID)){
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, ["there is no post with this post_id"]);
        }
        delete_post_meta($post_id, 'peepso_pinned');
        delete_post_meta($post_id, 'peepso_pinned_by');

        if( 1 == $pin_status ) {
            add_post_meta($post_id, 'peepso_pinned', current_time('timestamp', TRUE), TRUE);
            add_post_meta($post_id, 'peepso_pinned_by', get_current_user_id(), TRUE);
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['message'=>"this post has been pinned successfully"]);
        }
        return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['message'=>"this post has been unpinned successfully"]);
    }

    public function report(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $_POST['uid'] = get_current_user_id();
        $PeepSoActivity   = new PeepSoActivity();
        $resp = new PeepSoAjaxResponse();
        $PeepSoActivity->report($resp);
        if($resp->success){
            $resp->set('msg',count($resp->notices)?$resp->notices[0]:__('This item has been reported', 'peepso-core'));
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);

    }

    public function submit_vote(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());

        $PeepSoPollsAjax = PeepSoPollsAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoPollsAjax->submit_vote($resp);
        if($resp->success){
            $resp->set('msg',__('Vote added.', 'peepso-core'));
            unset($resp->data['html']);
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);
    }

    public function change_vote(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());

        $PeepSoPollsAjax = PeepSoPollsAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoPollsAjax->change_vote($resp);
        if($resp->success){
            $resp->set('msg',__('Vote retrived', 'peepso-core'));
            unset($resp->data['html']);
            $post_id = $_POST['poll_id'];
            $user_id = $_POST['user_id'];
            $polls_model = new PeepSoPollsModel();
		    $user_polls = $polls_model->get_user_polls($user_id, $post_id);

            $max_answers = (int) get_post_meta($post_id, 'max_answers', TRUE);
            $options = unserialize(get_post_meta($post_id, 'select_options', TRUE));
            $total_user_poll = get_post_meta($post_id, 'total_user_poll', TRUE);

            $resp->data = array(
                'id' => $post_id,
                'options' => (is_array($options) && count($options) > 1) ? $options : array(),
                'type' => $max_answers === 0 ? 'checkbox' : 'radio',
                'enabled' => TRUE,
                'is_voted' => FALSE,
                'total_user_poll' => $total_user_poll,
                'user_polls' => $user_polls?$user_polls:null
            );
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);
    }

    public function unvote(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());

        $PeepSoPollsAjax = PeepSoPollsAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoPollsAjax->unvote($resp);
        if($resp->success){
            $resp->set('msg',__('Vote deleted.', 'peepso-core'));
            unset($resp->data['html']);
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);
    }
}
