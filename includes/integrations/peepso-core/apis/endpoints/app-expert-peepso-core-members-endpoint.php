<?php
class App_Expert_Peepso_Core_Members_Endpoint{

    public function follow(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $ajax = PeepSoFollowerAjax::get_instance();
        $resp  = new PeepSoAjaxResponse();
        $ajax->set_follow_status($resp);
        $val=$request->get_param('follow');
        $msg=__("un-follow done successfully",'app-expert');
        if($val) $msg=__("follow done successfully",'app-expert');
        if($resp->success){
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,['message'=>$msg]);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);
    }

    public function search_members(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $_POST['no_html'] = 1 ;
        $ajax = PeepSoMemberSearch::get_instance();
        $resp = new PeepSoAjaxResponse();

        $ajax->search($resp);

        if($resp->success){
            $users=[];
            foreach ($resp->data['no_html_data'] as $u){
                $users[]=(new App_Expert_User_Serializer($u))->get();
            }
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$users);
        }else{
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,[]);
        }

    }

}