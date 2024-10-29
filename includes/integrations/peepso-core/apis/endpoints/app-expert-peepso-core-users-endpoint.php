<?php
class App_Expert_Peepso_Core_Users_Endpoint{
    public function get_user(WP_REST_Request $request){

        $id = $request->get_param('id')??0;
        $PeepSoUser  = PeepSoUser::get_instance($id);
        $userData=(new App_Expert_User_Serializer($PeepSoUser))->get();

        $profile_tabs=apply_filters('peepso_navigation_profile', array('_user_id'=>get_current_user_id()));
        $userData['profile_tabs']=[];
        foreach ($profile_tabs as $tabname=>$tab){
            $userData['profile_tabs'][]=[
                'id'=>$tabname,
                'label'=>$tab['label'],
                'url'=>$PeepSoUser->get_profileurl().$tab['href'],
                'sub_items'=>  apply_filters('ae_peepso_profile_tab_sub_items',[],$tabname,$PeepSoUser,$PeepSoUser->get_profileurl())
            ];
        }
        return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$userData);
    }

    public function edit_profile_image(WP_REST_Request $request){

        $PeepSoUser  = PeepSoUser::get_instance(0);
        $PeepSoUser->move_avatar_file($_FILES['profile_image']['tmp_name']);
        $PeepSoUser->finalize_move_avatar_file();

        return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ["message"=>'profile image has been changed succesfully' , 'url' => $PeepSoUser->get_avatar()]);
    }

    public function edit_coverImage(WP_REST_Request $request){
        $PeepSoUser  = PeepSoUser::get_instance(0);
        $PeepSoUser->move_cover_file( $_FILES['cover_image']['tmp_name']);
        return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ["message"=>'cover image has been changed succesfully' , 'url' => $PeepSoUser->get_cover()]);
    }

    public function report(WP_REST_Request $request){
       // profile.report
        $peepso_profile = PeepSoProfile::get_instance();
        $_POST = array_merge($_POST,$request->get_params());
        $resp = new PeepSoAjaxResponse();
        $peepso_profile->report($resp);
        if($resp->success){
            $resp->set('msg',count($resp->notices)?$resp->notices[0]:__('This item has been reported', 'peepso-core'));
            return App_Expert_Peepso_Core_Response_Helper::success_response( App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);

    }

    public function get_followers(WP_REST_Request $request) {
        $user_id = $request->get_param('user_id')?:get_current_user_id();
        $page = $request->get_param('page')?:1;

        // default limit is 1 (NewScroll)
        $limit = $request->get_param('limit')?:1;

        $offset = ($page - 1) * $limit;

        if ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        $users =  PeepSoUserFollower::get_followers([
            'offset' => $offset,
            'limit' => $limit,
            'user_id' => $user_id
        ]);
        foreach ($users as $i=>$u){
            $users[$i] = (new App_Expert_User_Serializer(PeepSoUser::get_instance($u->uf_active_user_id)))->get();
        }
        return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$users);

    }


    public function get_following(WP_REST_Request $request) {
        $user_id = $request->get_param('user_id')?:get_current_user_id();
        $page = $request->get_param('page')?:1;

        // default limit is 1 (NewScroll)
        $limit = $request->get_param('limit')?:1;

        $offset = ($page - 1) * $limit;

        if ($page < 1) {
            $page = 1;
            $offset = 0;
        }

        $users =  PeepSoUserFollower::get_following([
            'offset' => $offset,
            'limit' => $limit,
            'user_id' => $user_id
        ]);
        foreach ($users as $i=>$u){
            $users[$i] = (new App_Expert_User_Serializer(PeepSoUser::get_instance($u->uf_passive_user_id)))->get();
        }
        return App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$users);

    }


}