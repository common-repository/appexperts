<?php

class App_Expert_Peepso_Photos_Post {
    public function __construct(){
        add_filter("ae_peepso_post_object",array($this,'add_user_edit'),10,2);
    }
    public function add_user_edit($result,$peepso_post){
        if(class_exists('PeepSoPhotosModel')){
            $img_ins = new PeepSoPhotosModel();
            $imgs = $img_ins->get_post_photos($peepso_post['ID']);
            if(!empty($imgs)){
                $result['type']='images';
                $result['images_data']=$imgs;
                $photo_type = get_post_meta($peepso_post['ID'], PeepSoSharePhotos::POST_META_KEY_PHOTO_TYPE, true);
                $actions =['text'=>'','parameters'=>[],'photo_type'=>$photo_type];
                switch ($photo_type){
                    case PeepSoSharePhotos::POST_META_KEY_PHOTO_TYPE_AVATAR:
                        $actions['text'] = __(' uploaded a new avatar', 'picso');
                        $actions['text'] = apply_filters('peepso_photos_stream_action_change_avatar', $actions['text'], $peepso_post['ID']);
                        break;
                    case PeepSoSharePhotos::POST_META_KEY_PHOTO_TYPE_COVER:
                        $actions['text'] = __(' uploaded a new profile cover', 'picso');
                        $actions['text'] = apply_filters('peepso_photos_stream_action_change_cover', $actions['text'], $peepso_post['ID']);
                        break;
                    case PeepSoSharePhotos::POST_META_KEY_PHOTO_TYPE_ALBUM:
                        $actions['text'] = apply_filters('peepso_photos_stream_action_photo_album', $actions['text'], $peepso_post['ID']);

                        // modify action if only its empty
                        if($actions['text'] == '')
                        {
                            $photos_album_model = new PeepSoPhotosAlbumModel();

                            // [USER] added [photo/photos] to [ALBUM NAME] album
                            $total_photos = get_post_meta($peepso_post['ID'], PeepSoSharePhotos::POST_META_KEY_PHOTO_COUNT, true);
                            $album = $photos_album_model->get_photo_album($peepso_post['post_author'], 0, $peepso_post['ID']);

                            // generate link
                            $user = PeepSoUser::get_instance($peepso_post['post_author']);

                            $actions['text'] = _n(' added %1$d photo to the album: ', ' added %1$d photos to the album: ', $total_photos, 'picso');
                            $actions['parameters']['total_photos']   = $total_photos;
                            $actions['parameters']['pho_album_name'] = $album[0]->pho_album_name;
                            $actions['parameters']['pho_album_id']   = $album[0]->pho_album_id;

                        }
                        break;
                    default:
                        $_photos_model =new PeepSoPhotosModel();
                        $total_photos = $_photos_model->count_post_photos($peepso_post['ID']);
                        $actions['text'] = _n(' uploaded %1$d photo', ' uploaded %1$d photos', $total_photos, 'picso');
                        $actions['parameters']['total_photos']   = $total_photos;
                        break;
                }
                $result['post_action']=$actions;
            }
        }
        return $result;
    }
}
new App_Expert_Peepso_Photos_Post();
