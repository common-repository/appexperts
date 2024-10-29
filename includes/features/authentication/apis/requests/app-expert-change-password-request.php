<?php
class App_Expert_Change_Password_Request  extends  App_Expert_Auth_Request{
    public function rules(){
        return [
            'current_password'      => 'check_current_password',
            'new_password'          => 'check_new_password',
            'password_confirmation' => 'check_confirmation_password',
        ];
    }
    public function check_current_password(){
        $current_password = (string) $this->request->get_param('current_password');

        if (is_wp_error($current_password))
            return $current_password;

        $user_id = get_current_user_id();
        $user = get_user_by('ID' , $user_id);

        if (is_wp_error($user))
            return $user;



        if (!wp_check_password($current_password , $user->user_pass , $user_id)){
            return new WP_Error(
                'app_expert_current_password_mismatch',
                __( 'Current password is invalid.' , 'app-expert' ),
                array( 'status' => 400 ) );
        }

        return $current_password;

    }

    public function check_new_password(){
        $value = $this->request->get_param('current_password');
        $new_password = (string) $value;

        $new_password = App_Expert_Password_Helper::general_password_check($new_password);


        if ( empty(trim($value)) ){
            $new_password = new WP_Error(
                'app_expert_new_password_not_present',
                __( 'New password parameter is not provided', 'app-expert' ),
                array(
                    'status' => 400,
                )
            );
        }



        if (is_wp_error($new_password))
            return $new_password;



        return  $new_password;
    }

    public function check_confirmation_password(){
        $value = $this->request->get_param('password_confirmation');
        $confirmation_password = (string) $value;

        $password = $this->request->get_params()['new_password'];

        $confirmation_password = App_Expert_Password_Helper::general_password_check($confirmation_password);

        if (is_wp_error($confirmation_password))
            return $confirmation_password;

        if ( !isset($password) || empty($password) || $password != $confirmation_password ) {
            return new WP_Error( 'rest_user_mismatch_password', __( 'Both passwords must match' , 'app-expert' ), array( 'status' => 400 ) );
        }

        return $confirmation_password;
    }

}