<?php

class App_Expert_Peepso_Photos_Comment {
    public function __construct(){
        add_filter("ae_peepso_comment_object",array($this,'add_user_edit'),10,2);
        add_filter("ae_peepso_handle_add_comment_request",array($this,'add_comment_image'),10,2);
        add_filter("ae_peepso_handle_update_comment_request",array($this,'add_comment_image'),10,2);
        add_filter("ae_peepso_add_comment_parameters",array($this,'add_comment_parameters'),10,1);
        add_filter("ae_peepso_update_comment_parameters",array($this,'add_comment_parameters'),10,1);
    }
    public function add_user_edit($result,$peepso_comment){
        // get a comment image url if exist
        $post = get_post($peepso_comment["ID"]);
        $peepso_media = get_post_meta($post->ID, 'peepso_photo_comments');
        if($peepso_media){

            $photo = json_decode($peepso_media[0]);
            $photo_name = $photo->thumbs->m;

            $file_path = str_replace( "\\", "/", str_replace( str_replace( "/", "\\", WP_CONTENT_URL ), "", "/peepso/users/$post->post_author/photos/thumbs/".$photo_name ) );
            $photo_url = content_url( $file_path );
            $result['image_url'] = $photo_url;
            $result['Image_file_name'] = $photo->filesystem_name;
        }
        return $result;
    }
    public function add_comment_image($posted,$request){
        if($request->has_param('filedata')){
            $posted['photo'] = $request->get_param('filedata');
        }
        return $posted;
    }
    public function add_comment_parameters($params){
        $params['content']['required']=false;
        $params['filedata']=array(
            'description' => __( 'comment image' , 'app-expert' ),
            'type'        => 'string',
            'required' => false
        );
        return $params;
    }
}
new App_Expert_Peepso_Photos_Comment();
