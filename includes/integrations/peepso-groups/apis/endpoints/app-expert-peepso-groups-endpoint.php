<?php
class App_Expert_Peepso_Groups_Endpoint
{
    public function is_active(WP_REST_Request $request){
        if(in_array('peepso-groups/groups.php', apply_filters('active_plugins', get_option('active_plugins')))){ 
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['peepso group plugin is active']);
        }else{
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, ['peepso group plugin is not active']);
        } 
    }
    public function add(WP_REST_Request $request){
        $group_data = array(
            'name'			=> $request->get_param('name')??'', // SQL safe
            'owner_id'		=> get_current_user_id(),
            'meta'			=> array('privacy'=>$request->get_param('privacy')), // SQL safe,
        );

        if(PeepSo::get_option('groups_categories_enabled', FALSE)) {
            $group_data['category_id'] = $request->get_param('category_id')?? array();
        }

        // respect line breaks
        $description =  $request->get_param('description')??''; // SQL Safe
        $description = htmlspecialchars($description);
        $group_data['description'] = trim(PeepSoSecurity::strip_content($description));

        $group = new PeepSoGroup(NULL, $group_data);
        $groupsData=(new App_Expert_Group_Serializer($group))->get(false);

        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $groupsData);

    }

    public function get(WP_REST_Request $request){
        $page_number = $request->get_param('page')??1;
        $limit = $request->get_param('limit')??10;
        $offset = $limit*($page_number-1);
        $orderby = $request->get_param('orderby')??'post_title';
        $sort = $request->get_param('sort')??'desc';
        $search = $request->get_param('search')??'';
        $user_id = $request->get_param('user_id')??0;
        $category = $request->get_param('category')??0;
        $search_mode = $request->get_param('search_mode')??'exact';
        
        $PeepSoGroups  = new PeepSoGroups();
        $allGroupsData = $PeepSoGroups->get_groups($offset, $limit, $orderby , $sort, $search, $user_id, $category, $search_mode );
        $groupsData = [];
        
        foreach($allGroupsData as $groupData){
            $groupsData[]=(new App_Expert_Group_Serializer($groupData))->get();
        }
        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $groupsData);

    }

    public function get_one(WP_REST_Request $request){
        $id =  $request->get_param('id');
        $group = new PeepSoGroup($id);
        if ($group){
            $groupData= (new App_Expert_Group_Serializer($group))->get(false);
            $PeepSoGroupUser= new PeepSoGroupUser($group->id);
            $segments = array();
            $segments[0][] = array(
                'href' => 'stream',
                'title'=> __('Stream', 'groupso'),
                'icon' => 'gcis gci-stream',
            );
            if($PeepSoGroupUser->is_member){
                if($PeepSoGroupUser->can('manage_group')) {
                    $segments[0][] = array(
                        'href' => 'settings',
                        'title' => __('Settings', 'groupso'),
                        'icon' => 'gcis gci-cog',
                    );
                }

                $segments[0][] = array(
                    'href' => 'members',
                    'title'=> __('Members', 'groupso'),
                    'count'=>$pending = $group->pending_admin_members_count,
                    'icon' => 'gcis gci-user-friends',
                );
                $segments = apply_filters('peepso_group_segment_menu_links', $segments);
            }

            $groupData['profile_tabs']=[];
            foreach ($segments as $tabs){
                foreach ($tabs as $tab){
                        $tabname=strtolower($tab['href']);
                        if(empty($tabname)) $tabname=strtolower($tab['title']);
                        $groupData['profile_tabs'][]=[
                            'id'=>$tabname,
                            'label'=>$tab['title'],
                            'count'=>(int)($tab['count']??0),
                            'url'=>$group->get_url().$tab['href'],
                            'sub_items'=> App_Expert_Peepso_Groups_Tabs_Helper::getTabSubItems($tabname,$group,$group->get_url())
                        ];
                }
            }

            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $groupData);


        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_NOT_FOUND, ["no group found with this id."]);

    }
    
    public function get_categories(WP_REST_Request $request){

        $PeepSoGroupCategories  = new PeepSoGroupCategories();
        $categories = $PeepSoGroupCategories->categories;
       
        $catagoriesData = [];

        if(isset($categories[-1])){
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, ["no categories found. "]);
        }
            
        foreach($categories as $category){
            $catagoriesData[]=(new App_Expert_Category_Serializer($category))->get();
        }

        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $catagoriesData);

    }

    public function change_avatar(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());
        $_POST['_wpnonce'] = wp_create_nonce("group-avatar") ;
        $_POST['module_id'] = PeepSoGroupsPlugin::MODULE_ID;

        $PeepSoGroupAjax   =  PeepSoGroupAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoGroupAjax->avatar_upload($resp);
        if($resp->success){
            $resp2 = new PeepSoAjaxResponse();
            $PeepSoGroupAjax->avatar_confirm($resp2);
            if($resp2->success){
                $resp->data=[];
                $resp->set('message',__('group avatar has been updated successfully.', 'peepso-core'));
                return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);
            }
        }

        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, ["group avatar is not updated successfully"]);
    }

    public function change_cover(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());
        $_POST['_wpnonce'] = wp_create_nonce("group-cover") ;
        $_POST['module_id'] = PeepSoGroupsPlugin::MODULE_ID;

        $PeepSoGroupAjax   =  PeepSoGroupAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoGroupAjax->cover_upload($resp);

        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('group cover has been updated successfully.', 'peepso-core'));
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);

        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, $resp->errors);
    }

    public function group_members(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());
        $ajax = PeepSoGroupUsersAjax::get_instance();
        $rep = new PeepSoAjaxResponse();
        $ajax->search($rep);
        $data = [];
        if($rep->success&&isset($rep->data['members'])){
            foreach ($rep->data['members'] as $user){
                $data[]=(new App_Expert_Member_Serializer(new PeepSoGroupUser($request->get_param('group_id'),$user['id'])))->get();
            }
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $data);   
        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, $rep->errors);
    }

    public function change_settings(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());
        $_POST['group_id'] = $request->get_param('group_id');
        
        $PeepSoGroupAjax   =  PeepSoGroupAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        if($request->get_param('name')){
            $_POST['_wpnonce'] = wp_create_nonce("set-group-name") ;
            $PeepSoGroupAjax->set_group_name($resp);
            
        }

        if($request->get_param('description')){
            $_POST['_wpnonce'] = wp_create_nonce("set-group-description") ;
            $PeepSoGroupAjax->set_group_description($resp);
      
        }

        if($request->get_param('properties')){
            $properties = json_decode($request->get_param('properties'));
            foreach( $properties as $key => $obj){

                $_POST['property_name'] = $obj->property_name;;
                $_POST['property_value'] = $obj->property_value;
                $_POST['_wpnonce'] = wp_create_nonce("set-group-property") ;
                $PeepSoGroupAjax->set_group_property($resp);
                
            }
        }

        if($request->get_param('category_id')){
 
            $_POST['_wpnonce'] = wp_create_nonce("set-group-categories") ;
            $PeepSoGroupAjax->set_group_categories($resp);
        }

        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('group setting has been updated successfully.', 'peepso-core'));
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $resp->data);

        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, ["group setting is not updated successfully"]);

    }

    public function invite(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $rep = new PeepSoAjaxResponse();
        $ajax = PeepSoGroupUsersAjax::get_instance();
        $ajax->search_to_invite($rep);
        $data = [];
        $group_user = new PeepSoGroupUser($request->get_param('group_id'));
        // Find site users who do not have a record inside group_members for this group ID
        if (!$group_user->can('invite')) {
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, ['Unauthorized to apply this action.']);

        }
        if($rep->success&&isset($rep->data['users'])){
            foreach ($rep->data['users'] as $user){
                $data[]=(new App_Expert_User_Serializer(PeepsoUser::get_instance($user['id'])))->get();
            }
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $data);

        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, []);

    }

    public function passive_invite(WP_REST_Request $request){
        $group_user = new PeepSoGroupUser($request->get_param('group_id'));
        if (!$group_user->can('invite')) {
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, [__('Unauthorized to apply this action.')]);
        }

        $not_title = __('invited you to join a group', 'groupso');
        $error = __('Unable to invite this user');

        $_group_id =$request->get_param('group_id');
        $_passive_user_id =$request->get_param('user_id');
        $_passive_model= new PeepSoGroupUser($_group_id, $_passive_user_id);
        $group_user = new PeepSoGroupUser($_group_id,$_passive_user_id);
        if($group_user->is_member){
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, [__('the user is already a member.')]);

        }
        if($group_user->is_pending_user){
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, [__('the user is already added.')]);
        }

        $data=[];
        if(App_Expert_Peepso_Core_Allowed_Plugins_Helper::is_admin() && 1 == PeepSo::get_option('groups_add_by_admin_directly', 0)) {
            if($success = $_passive_model->member_add()) {
                do_action('peepso_action_group_add', $_passive_model->group_id, $_passive_model->user_id);
            }
            $not_title = __('added you to a group', 'groupso');
            $error = __('Unable to add this user');
        } else {
            if($success = $_passive_model->member_invite()) {

                $PeepSoGroupUsers = new PeepSoGroupUsers($_group_id);
                $data['member_count']= $PeepSoGroupUsers->update_members_count();
                $data['pending_user_member_count']= $PeepSoGroupUsers->update_members_count('pending_user');
                do_action('peepso_action_group_user_invitation_send', $_passive_model);
            }
        }
        if($success) {
            $PeepSoNotifications = new PeepSoNotifications();
            $PeepSoNotifications->add_notification(get_current_user_id(), $_passive_user_id, $not_title, 'groups_user_invitation_send', PeepSoGroupsPlugin::MODULE_ID, $_group_id);
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $data);

        } else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, [$error]);

    }

     public function follow(WP_REST_Request $request){
        $_model = new PeepSoGroupFollower($request->get_param('group_id'));
        $actions= [];
        if($request->has_param('follow')) $actions['follow']=$request->get_param('follow');

        if($request->has_param('notify')) $actions['notify']=$request->get_param('notify');

        if($request->has_param('email') ) $actions['email']=$request->get_param('email');

        foreach ($actions as $prop=>$value){
            $success = $_model->set( $prop, $value );

            // Force disable e-mails if on-site is disabled
            if($prop == 'notify' && 0 == $value) {
                $_model->set( 'email', 0 );
            }

            // Force enable on-site if e-mail is enabled
            if($prop == 'email' && 1 == $value) {
                $_model->set( 'notify', 1 );
            }
        }

        $actions=$_model->get_follower_actions();
        $data=[
            'actions'=>$actions?$actions[0]:[],
            'follower_data' =>[
                'follow' => (string)$_model->follow,
                'notify' => (string)$_model->notify,
                'email'  => (string)$_model->email
            ]
        ];
        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $data);

    }

    public function actions(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $ajax = PeepSoGroupUserAjax::get_instance();
        $rep = new PeepSoAjaxResponse();

        $sent_action=$request->get_param('gb_action');
        $actions=[
            'groupuserajax.leave'=>'leave',
            'groupuserajax.passive_modify'=>'passive_modify',
            'invite'=>'passive_invite',
            'join'=>'join',
            'leave'=>'leave',
            'cancel_request_to_join'=>'cancel_request_to_join',
            'join_request'=>'join_request'
        ];
        $action = $actions[$sent_action];
        $ajax->$action($rep);
        if($rep->success){
            $PeepSoGroupUser= new PeepSoGroupUser($request->get_param('group_id'));
            $data=['actions'=>App_Expert_Peepso_Groups_Action_Helper::getActions($PeepSoGroupUser->get_member_actions())];
            if($request->has_param('passive_user_id')){
                $group_user= new PeepSoGroupUser($request->get_param('group_id'),$request->get_param('passive_user_id'));
                $data['actions_passive'] = App_Expert_Peepso_Groups_Action_Helper::getActions($group_user->get_member_passive_actions(['manage_user_member_owner','manage_user_member','manage_user_member_manager','manage_user_member_moderator','manage_user_delete','manage_user_banned']));
            }
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $data);

        }else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, $rep->errors);
    }

}
