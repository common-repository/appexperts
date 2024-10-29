<?php

class App_Expert_Peepso_Photos_Endpoint
{
    public function upload_photo(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        if($request->get_param('group_id')){
            $_POST['group_id'] = (int)$_POST['group_id'];
        }
        $_POST = apply_filters('ae_peepso_handle_upload_photo_request',$_POST , $request);

        $PeepSoPhotosAjax   =  PeepSoPhotosAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoPhotosAjax->upload_photo($resp);

        if($resp->success){
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        }
        return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);

    }

    public function add(WP_REST_Request $request){

        $_POST = array_merge($_POST,$request->get_params());
        $_POST['_wpnonce'] = wp_create_nonce("photo-create-album") ;
        $_POST['user_id'] = get_current_user_id();
        $_POST['type'] = 'album';
        if($request->get_param('group_id')){
            $_POST['group_id'] = (int)$_POST['group_id'];
        }
        $_POST = apply_filters('ae_peepso_handle_add_album_request', $_POST, $request);

        $PeepSoPhotosAjax   =  PeepSoPhotosAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoPhotosAjax->create_album($resp);

        if($resp->success){
            $resp->set('message',__('album has been created successfully.', 'peepso-core'));
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);

        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,$resp->errors);

    }

    public function get(WP_REST_Request $request){
        $user_id = $request->get_param('user_id')??get_current_user_id();
        $module_id = 0;
        $data=[
            'user_id'=>$user_id,
            'module_id'=>$module_id
        ];
        if($request->get_param('group_id')){
            $_GET['group_id'] = (int)$_GET['group_id'];
        }
        $data = apply_filters('ae_peepso_handle_get_album_request',$data, $request);

        if(!empty($data)){
            $user_id   = $data['user_id'];
            $module_id = $data['module_id'];
        }

        $page_number = $request->get_param('page')??1;
        $limit = $request->get_param('limit')??10;
        $offset = $limit*($page_number-1)??0;
        $sort = $request->get_param('sort')??'desc';
        $PeepSoPhotosAlbumModel   =  new PeepSoPhotosAlbumModel();
        $user_albums = $PeepSoPhotosAlbumModel->get_user_photos_album($user_id, $offset, $limit, $sort, $module_id);

//        if(!$user_albums){
//            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,["no albums found. "]);
//        }

        $albumsData = [];

        foreach($user_albums as $key => $album){
            $album_id = $album->pho_album_id;
            $user_albums[$key]->num_of_photos = $PeepSoPhotosAlbumModel->get_num_photos_by_album($user_id, $album_id, $module_id);
            $photos = $PeepSoPhotosAlbumModel->get_album_photo($user_id, $album_id, $offset, $limit , $sort , $module_id);
        
            $albumPhotos = [];
            foreach ($photos as $photo) {
                $act_id =null;
                $PeepSoActivity   = new PeepSoActivity();
                $post =  $PeepSoActivity->get_activity_data($photo->pho_id+0,PeepSoSharePhotos::MODULE_ID);
                if($post){
                   $act_id = $post->act_id;
                }
                
                $albumPhotos[] = ['name' => $photo->pho_file_name , 'photo_post_id' => $photo->pho_id, 'act_id' => $act_id, 'photo_url' => $photo->location, 'photo_thumbs' => $photo->pho_thumbs ];
            }
            $user_albums[$key]->photos = $albumPhotos;
            $albumsData[] = (new App_Expert_Album_Serializer($album,$module_id))->get();
        }
        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$albumsData);

    }

    public function delete(WP_REST_Request $request){
        $_POST = array_merge($_POST,$request->get_params());
        $_POST['_wpnonce'] = wp_create_nonce('photo-delete-album') ;
        $_POST['uid'] = get_current_user_id();
        if($request->get_param('group_id')){
            $_POST['group_id'] = (int)$_POST['group_id'];
        }
        $_POST = apply_filters('ae_peepso_handle_delete_album_request', $_POST, $request);

        $photos_album_model = new PeepSoPhotosAlbumModel();
        $album=$photos_album_model->get_album($request->get_param('album_id'),$_POST['uid']);
        if(!$album){
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST,["Album not found"]);
        }
        $new_act_id = (new PeepSoPhotosModel())->get_photo_activity($album->pho_post_id);
        $flag=$new_act_id?PeepSo::check_permissions(intval($album->pho_owner_id), PeepSo::PERM_POST_DELETE, get_current_user_id()):false;
        if(!$flag){
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_UNAUTHORIZED,["Delete Album not Allow"]);

        }
        $PeepSoPhotosAjax   =  PeepSoPhotosAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoPhotosAjax->delete_album($resp);

        if($resp->success)
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);

        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, $resp->errors);

    }

    public function edit(WP_REST_Request $request){
      
        $_POST['album_id'] = $request->get_param('album_id') ;
        $allowUpdatePrivacy =  apply_filters('ae_peepso_handle_edit_album_allow_update_privacy',true,$request);
        $_POST['user_id'] = get_current_user_id();
        if($request->get_param('group_id')){
            $_POST['group_id'] = (int)$request->get_param('group_id');
        }
        $_POST = apply_filters('ae_peepso_handle_edit_album_request',$_POST ,$request);
        $_POST['owner_id'] = $_POST['user_id'];

        $photos_album_model = new PeepSoPhotosAlbumModel();
        $album=$photos_album_model->get_album( $_POST['album_id'] , $_POST['owner_id']);
        if(!$album){
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_NOT_FOUND, ["Album not found"]);

        }

        $PeepSoPhotosAjax   =  PeepSoPhotosAjax::get_instance();
        $resp = new PeepSoAjaxResponse();

        if($request->has_param('name'))
        {
            $_POST['_wpnonce'] = wp_create_nonce("set-album-name") ;
            $_POST['name'] = $request->get_param('name') ;
            $PeepSoPhotosAjax->set_album_name($resp);
        }

        if($request->has_param('description'))
        {
            $_POST['_wpnonce'] = wp_create_nonce("set-album-description") ;
            $_POST['description'] = $request->get_param('description') ;
            $PeepSoPhotosAjax->set_album_description($resp);
        }

        if($allowUpdatePrivacy&&$request->has_param('privacy'))
        {
            $_POST['_wpnonce'] = wp_create_nonce("set-album-access") ;
            $_POST['acc'] = $request->get_param('privacy') ;
            $PeepSoPhotosAjax->set_album_access($resp);
        }

        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('album has been updated successfully.', 'peepso-core'));
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);
        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, $resp->errors);
    }

    public function add_photos(WP_REST_Request $request){
      
        $_POST['_wpnonce'] = wp_create_nonce("photo-add-to-album") ;
        $_POST['user_id'] = get_current_user_id();
        $_POST['album_id'] = $request->get_param('album_id') ;
        $_POST['type'] = 'photo';
        if($request->get_param('group_id')){
            $_POST['group_id'] = (int)$_POST['group_id'];
        }
        $_POST = apply_filters('ae_peepso_handle_add_photos_album_request', $_POST, $request);
        $_POST['photo'] = $request->get_param('photo') ;

        $PeepSoPhotosAjax   =  PeepSoPhotosAjax::get_instance();
        $resp = new PeepSoAjaxResponse();
        $PeepSoPhotosAjax->add_photos_to_album($resp);
        
        if($resp->success){
            $resp->data=[];
            $resp->set('message',__('album has been updated successfully.', 'peepso-core'));
            return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$resp->data);

        }
        else
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_BAD_REQUEST, $resp->errors);

    }

    public function get_photos(WP_REST_Request $request){
        $limit = $request->get_param('limit')??10;

        $page = $request->get_param('page')??1;
        $sort = $request->get_param('sort')??'desc';

        $offset = ($page - 1) * $limit;

        if ($page < 1) $offset = 0;

        $owner = $request->get_param('user_id')??get_current_user_id();
        $data = array(
            'owner_id' => $owner,
            'module_id' => 0
        );
        if($request->get_param('group_id')){
            $_GET['group_id'] = (int)$_GET['group_id'];
        }
        $data = apply_filters('ae_peepso_handle_get_photos_album_request',$data, $request);

        if(!empty($data)){
            $owner   = $data['owner_id'];
            $module_id = $data['module_id'];
        }

        $photos_model = new PeepSoPhotosModel();
        $photos = $photos_model->get_user_photos($owner, $offset, $limit, $sort, $module_id);
        $data=[];
        if (count($photos)) {
            foreach ($photos as $photo) {
                // checking batch upload
                $new_act_id = $photos_model->get_photo_activity($photo->pho_id);
                $data[]= [
                    'name' => $photo->pho_file_name ,
                    'photo_post_id' => $photo->pho_id,
                    'act_id' => $new_act_id,
                    'photo_url' => $photo->location,
                    'photo_thumbs' => $photo->pho_thumbs,
                    'photo'=>$photo
                ];
            }
        }
        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS,$data);
    }

    public function get_album_photos(WP_REST_Request $request){
        $limit = $request->get_param('limit')??10;

        $page = $request->get_param('page')??1;
        $sort = $request->get_param('sort')??'desc';

        $offset = ($page - 1) * $limit;

        if ($page < 1) $offset = 0;

        $owner = $request->get_param('user_id')??get_current_user_id();
        $album_id= $request->get_param('album_id');
        $data = array(
            'owner_id' => $owner,
            'module_id' => 0
        );
        if($request->get_param('group_id')){
            $_GET['group_id'] = (int)$_GET['group_id'];
        }
        $data = apply_filters('ae_peepso_handle_get_photos_by_album_album_request',$data ,$request);

        if(!empty($data)){
            $owner   = $data['owner_id'];
            $module_id = $data['module_id'];
        }
        
        $photos_album_model = new PeepSoPhotosAlbumModel();

        $album=$photos_album_model->get_album_by_id( $album_id );
        if(!$album){
            return  App_Expert_Peepso_Core_Response_Helper::fail_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_NOT_FOUND, ["Album not found"]);
        }
        $photos = $photos_album_model->get_album_photo($owner,$album_id, $offset, $limit, $sort, $module_id);
        $data=[];
        $photos_model = new PeepSoPhotosModel();
        if (count($photos)) {
            foreach ($photos as $photo) {
                // checking batch upload
                $new_act_id = $photos_model->get_photo_activity($photo->pho_id);
                $data[] = [
                    'name' => $photo->pho_file_name,
                    'album' => $photo->pho_album_id,
                    'photo_post_id' => $photo->pho_id,
                    'act_id' => $new_act_id,
                    'photo_url' => $photo->location,
                    'photo_thumbs' => $photo->pho_thumbs
                ];
            }
        }
        return  App_Expert_Peepso_Core_Response_Helper::success_response(App_Expert_Peepso_Core_Response_Helper::STATUS_CODE_SUCCESS, $data);
    }

}
