<?php
class App_Expert_Register_Request extends App_Expert_Request{

    public function rules(){
        return [
            'username' =>'check_username',
            'first_name' =>'check_first_name',
            'last_name' =>'check_last_name',
            'password' =>'check_password',
            'password_confirmation' =>'check_confirm_password',
        ];
    }

    public function check_username() {
        $username = (string) $this->request->get_param('username');
        if(empty($username)) return true;

        if ( ! validate_username( $username ) ) {
            return new WP_Error(
                'rest_user_invalid_username',
                __( 'This username is invalid because it uses illegal characters. Please enter a valid username.' ),
                array( 'status' => 400 )
            );
        }

        /** This filter is documented in wp-includes/user.php */
        $illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

        if ( in_array( strtolower( $username ), array_map( 'strtolower', $illegal_logins ), true ) ) {
            return new WP_Error(
                'rest_user_invalid_username',
                __( 'Sorry, that username is not allowed.' ),
                array( 'status' => 400 )
            );
        }

        return true;
    }

    public function check_first_name(){
        $val = sanitize_text_field($this->request->get_param("first_name"));
        return !!$val;
    }

    public function check_last_name(){
        $val =  sanitize_text_field($this->request->get_param("last_name"));
        return !!$val;
    }

    public function check_password() {
        $password = (string) $this->request->get_param("password");

        if ( empty( $password ) ) {
            return new WP_Error(
                'rest_user_invalid_password',
                __( 'Passwords cannot be empty.' ),
                array( 'status' => 400 )
            );
        }

        if ( false !== strpos( $password, '\\' ) ) {
            return new WP_Error(
                'rest_user_invalid_password',
                sprintf(
                /* translators: %s: The '\' character. */
                    __( 'Passwords cannot contain the "%s" character.' ),
                    '\\'
                ),
                array( 'status' => 400 )
            );
        }

        return true;
    }

    public function check_confirm_password()
    {
        $confirm_password = (string) $this->request->get_param('password_confirmation');

        $password =  $this->request->get_param('password');

        if (empty($confirm_password)) {
            return new WP_Error('rest_user_invalid_confirm_password', __('Confirm Password cannot be empty.', 'app-expert'), array('status' => 400));
        }

        if (false !== strpos($confirm_password, '\\')) {
            return new WP_Error('rest_user_invalid_confirm_password', __('Confirm Password cannot contain the "\\" character.', 'app-expert'), array('status' => 400));
        }

        if (!isset($password) || empty($password) || $password != $confirm_password) {
            return new WP_Error('rest_user_mismatch_password', __('Both passwords must match', 'app-expert'), array('status' => 400));
        }

        return true;
    }
}