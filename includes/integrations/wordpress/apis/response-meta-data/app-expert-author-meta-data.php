<?php
class App_Expert_Author_Meta_Data   {

    public function __construct()
    {
        add_filter('ae_post_response',[$this,'attach_data']);
    }

    public  function attach_data(WP_REST_Response $response){
        $data = $response->get_data();
        if (!isset($data['author']) || empty($data['author']))
            return $response;

        $authors_ids = $data['author'];
        $users = array();
        if (is_array($authors_ids)){
            foreach ($authors_ids as $authors_id){
                $userdata = get_userdata($authors_id);
                if($userdata instanceof WP_User) $users[] =  $this->prepare_author_data($userdata);
            }
        }else{
            $userdata = get_userdata($authors_ids);
            if($userdata instanceof WP_User) $users[] =  $this->prepare_author_data($userdata);
        }
        $data['author'] = $users;

        $response->set_data($data);
        return $response;
    }

    /**
     * @param WP_User $user
     *
     * @return array
     */
    private function prepare_author_data(WP_User $user){
        return array(
            'id' => $user->ID,
            'name' => $user->display_name,
            'avatar_urls' => array(
                'small' => get_avatar_url( $user, array( 'size' => USER_IMAGE_SMALL ) ),
                'medium' => get_avatar_url( $user, array( 'size' => USER_IMAGE_MEDIUM ) ),
                'large' =>  get_avatar_url( $user, array( 'size' => USER_IMAGE_LARGE ) ),
            )
        );
    }

}
new App_Expert_Author_Meta_Data();

