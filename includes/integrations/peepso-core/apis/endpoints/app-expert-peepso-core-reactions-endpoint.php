<?php

class App_Expert_Peepso_Core_Reactions_Endpoint {

    public function add_reaction(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $ajax  = new PeepSoReactionsAjax();
        $resp  = new PeepSoAjaxResponse();
        $ajax->react($resp);
        if($resp->success)
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        else
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);

    }

    public function delete_reaction(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $ajax  = new PeepSoReactionsAjax();
        $resp  = new PeepSoAjaxResponse();
        $ajax->react_delete($resp);
        if($resp->success)
            return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        else
            return App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);

    }

    public function get_users_reactions(WP_REST_Request $request){
        global $wpdb;
        $react_id =  $request->get_param('react_id');
        $act_id   =  $request->get_param('act_id');
        $page     =  $request->get_param('page')??1;
        $limit    =  $request->get_param('limit')??10;
        $skip     = ($page-1)*$limit;
        $sql = "SELECT reaction_user_id  FROM `{$wpdb->prefix}" . PeepSoReactionsModel::TABLE . "`"
             . " WHERE `reaction_act_id`=%d "
             . " AND `reaction_type`=%d "
             . " LIMIT %d,%d"
        ;

        $sql = $wpdb->prepare($sql, $act_id, $react_id,$skip,$limit);
        $result = $wpdb->get_col($sql);
        $data = [];
        foreach ($result as $user){
            $PeepSoUser  = PeepSoUser::get_instance($user);
            $data[]=(new App_Expert_User_Serializer($PeepSoUser))->get();
        }
        return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$data);
    }


}
