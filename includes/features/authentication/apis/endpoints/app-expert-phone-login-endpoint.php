<?php

class App_Expert_Phone_Login_Endpoint
{

    public function __construct()
    {
        $this->namespace = APP_EXPERT_API_NAMESPACE;
    }

    public function check_user_phone( WP_REST_Request $request ) {


        $user_phone = $request->get_param( 'user_phone' );
        $found = false;

        $user_query = new WP_User_Query(
            array(
                'meta_key'    =>    'user_phone',
                'meta_value'    =>  $user_phone
            )
        );
        // Get the results from the query
        $users = $user_query->get_results();
        if($users){
            $found = true;
        }

        if(!$found){
            return new WP_Error(
                'user_phone_is_not_found',
                __( 'User Phone is not found', 'app-expert' ),
                array(
                    'status' => 422,
                    'found' => $found
                )
            );
        }else{
            $response['code'] = 'user_phone_is_found';
            $response['message'] = 'User Phone is found successfully';
            $response['data'] = [
                'status' => 200,
                'found' => $found
            ];
        }
        return $response;
    }


    /**
     * @param WP_REST_Request $request
     *
     * @return $response|WP_Error
     */
    public function login_by_mobile( WP_REST_Request $request ) {

        $fcm_access_token = $request->get_param( 'firebase_access_token' );
        $user  = App_Expert_Firebase_Login_Helper::get_user_from_access_token($fcm_access_token);
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
        $user_query = new WP_User_Query(
            array(
                'meta_key'    =>    'user_phone',
                'meta_value'    =>  $userPhoneNumber
            )
        );
        // Get the results from the query
        $users = $user_query->get_results();

        if(!$users){
            $username = 'us'.$userPhoneNumber;
            $password = 'ps'.$userPhoneNumber;
            $email = $userPhoneNumber.'@app-experts.com';
            $user_id = wp_create_user($username, $password, $email);
            add_user_meta( $user_id, 'user_phone', $userPhoneNumber);
            add_user_meta( $user_id, 'login_by_firebase', 1);
            add_user_meta( $user_id, 'is_profile_completed', 0);
            $user = get_user_by('ID', $user_id);
        }else{
            $user = $users[0];
        }

        return  App_Expert_User_Response_Helper::jwt_response($user,$request);
    }
}