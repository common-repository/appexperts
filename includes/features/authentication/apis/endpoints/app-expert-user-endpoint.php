<?php
class App_Expert_User_Endpoint extends  WP_REST_Users_Controller{
    public function get_current_item($request)
    {
        $user_id = get_current_user_id();
        $user     = $this->get_user($user_id);

        if (is_wp_error($user))
            return $user;

        $response = $this->prepare_item_for_response($user, $request);
        $response = rest_ensure_response($response);

        return App_Expert_Response::wp_rest_success(
            $response,
            'app_expert_retrieved_user',
            __('Profile data retrieved successfully.', 'app-expert')
        );
    }

    public function update_current_item($request){
        $user_id = get_current_user_id();

        if (isset($user_id) && !empty($user_id)) $request['id'] = $user_id;


        $request = App_Expert_Profile_Helper::remove_user_meta_data_update($request);

        $res = $this->update_user_meta($request, $user_id);
            if (is_wp_error($res))
                return $res;

        $response = parent::update_item($request);
        if (is_wp_error($response))
            return $response;

        return App_Expert_Response::wp_rest_success(
            $response,
            'app_expert_updated_user',
            __('Profile data updated successfully.', 'app-expert')
        );
    }
    public function prepare_item_for_response($user, $request)
    {
        return App_Expert_User_Response_Helper::prepare_user_data_for_response($user);
    }
    private function update_user_meta($request, $user_id)
    {

        $mobile_number = $request->get_param('phone');
        $firebase_access_token = $request->get_param('firebase_access_token');

        if(isset($firebase_access_token)){
            $user_logged_in_by_mobile = get_user_meta($user_id,'login_by_firebase', false);
            if(!empty($user_logged_in_by_mobile)){
                $user  = App_Expert_Firebase_Login_Helper::get_user_from_access_token($firebase_access_token);
                if(!isset($user) || !$user){
                    return new WP_Error(
                        'user_is_not_found',
                        __( 'User is not found', 'app-expert' ),
                        array(
                            'status' => 422,
                        )
                    );
                }
                $userPhoneNumber = $user['phoneNumber'];
                $user_info = get_userdata($user_id);
                $user_email = $user_info->user_email;
                $user_name = $user_info->user_login;

                if (strpos($user_email, '@app-experts.com') !== false) {
                    wp_update_user( array(
                            'ID' => $user_id,
                            'user_email' => $userPhoneNumber.'@app-experts.com'
                        )
                    );
                }

                if (strpos($user_name, 'us') !== false) {
                    global $wpdb;
                    $wpdb->update($wpdb->users, array('user_login' => 'us'.$userPhoneNumber,), array('ID' => $user_id));
                    update_user_meta( $user_id, 'nickname', $userPhoneNumber);
                    wp_update_user( array(
                            'ID' => $user_id,
                            'user_nicename' => 'us'.$userPhoneNumber,
                            'display_name' => 'us'.$userPhoneNumber
                        )
                    );
                }

                update_user_meta( $user_id, 'user_phone', $userPhoneNumber);
            }
        }

        if(isset($mobile_number)){
            $user_logged_in_by_mobile = get_user_meta($user_id,'login_by_firebase', false);
            if(empty($user_logged_in_by_mobile)){
                update_user_meta( $user_id, 'user_phone', $mobile_number);
            }

        }
        return $user_id;
    }

    public function delete_user(WP_REST_Request $request){

        $user_id=get_current_user_id();
        require_once(ABSPATH.'wp-admin/includes/user.php' );
        $success = wp_delete_user($user_id);
        return App_Expert_Response::success("app_expert_delete_user_success",
            'User deleted sueccssfully',["result"=>$success]
        );

    }
}