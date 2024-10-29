<?php 
/**
* ======================
* Author : Mahmoud Ramadan. 
* Date :6/14/21.    
* ======================
*/
class App_Expert_Album_Serializer {

    protected $album;
    protected $module_id;
    protected $albumKeys=[
        "pho_album_id", "pho_owner_id", "pho_post_id",
        "pho_album_acc", "pho_album_name", "pho_album_desc",
        "pho_created", "pho_system_album", "pho_module_id",
        "num_of_photos"
    ];

    public function __construct($album,$module_id=0){
        $this->album=$album;
        $this->module_id=$module_id;
    }
    public function get( ){
        
        $Arr=[];
        foreach($this->albumKeys as $key){
            $Arr[$key]= $this->album->$key;
        }
        $post=get_post($this->album->pho_post_id);
        $GLOBALS['post']=$post;

        $new_act_id = (new PeepSoPhotosModel())->get_photo_activity($this->album->pho_post_id);
        $Arr['reaction'] = App_Expert_Peepso_Album_Activity_Helper::getReactionCount($new_act_id);
        $Arr['act_id']=$new_act_id;

        if(intval($this->album->pho_system_album) === 0 ) {
            switch ($this->module_id) {
                case PeepSoGroupsPlugin::MODULE_ID:
                    $PeepSoActivity = PeepSoActivity::get_instance();
                    $act_id = $new_act_id;
                    $activity = $PeepSoActivity->get_activity($act_id);
                    $act_post = $PeepSoActivity->activity_get_post(NULL, $activity, $this->album->pho_owner_id, get_current_user_id());
                    setup_postdata($act_post);

                    $Arr['act_user_created'] = $activity->act_owner_id;
                    $Arr['current_user_id'] = get_current_user_id();
                    $Arr['can_edit'] = PeepSo::check_permissions(intval($activity->act_owner_id), PeepSo::PERM_POST_EDIT, get_current_user_id());
                    $Arr['can_delete'] = PeepSo::check_permissions(intval($activity->act_owner_id), PeepSo::PERM_POST_DELETE, get_current_user_id());
                    $Arr['can_upload'] = intval($activity->act_owner_id) === get_current_user_id();
                    break;
                default:
                    $Arr['can_edit'] = PeepSo::check_permissions(intval($this->album->pho_owner_id), PeepSo::PERM_POST_EDIT, get_current_user_id()) ;
                    $Arr['can_delete'] = PeepSo::check_permissions(intval($this->album->pho_owner_id), PeepSo::PERM_POST_DELETE, get_current_user_id()) ;
                    $Arr['can_upload'] = intval($this->album->pho_owner_id) === get_current_user_id();
            }
        } else{
            $Arr['can_edit']   = false;
            $Arr['can_delete'] = false;
            $Arr['can_upload'] = false;
        }
        $Arr['comment_count'] = !empty($this->album->pho_post_id)?(int)App_Expert_Peepso_Album_Activity_Helper::getCommentCount($this->album->pho_post_id):0;

        $photos_album_model = new PeepSoPhotosAlbumModel();
        $cover = $photos_album_model->get_album_photo(0, $this->album->pho_album_id, 0, 1, 'desc', $this->module_id);

        if(count($cover)>0){
            $Arr['thumbnail'] = $cover[0]->pho_thumbs["m"];
        }else{
            $Arr['thumbnail'] = PeepSo::get_asset('images/album/default.png');
        }


        return $Arr;
    }
}