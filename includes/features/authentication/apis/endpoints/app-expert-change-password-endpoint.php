<?php

class App_Expert_Change_Password_Endpoint extends  WP_REST_Controller {

    public function change_password(WP_REST_Request $request){
        $user_id = get_current_user_id();
        $password = $request->get_param('new_password');

        try{
            wp_set_password($password , $user_id  );
        }catch (Exception $exception){
            return new WP_Error(
                'app_expert_password_change_error',
                __( 'Something happened while setting password.' , 'app-expert' ),
                array( 'status' => 500 ) );
        }

        return array(
            'code' => 'app_expert_changed_user_password',
            'message' => __("Password changed successfully." , 'app-expert'),
            'data' => array(
                'status' => 200
            )
        );
    }
}