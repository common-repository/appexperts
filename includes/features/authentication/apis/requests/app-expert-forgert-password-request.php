<?php
class App_Expert_Forget_Password_Request extends App_Expert_Request {

    public function rules(){
        return [
            'email'=>'check_email'
           ];
    }
    public function check_email(){
        $email = (string) $this->request->get_param("email");

        $user_email_sanitized = sanitize_email( $email );

        if ( ! is_email( $user_email_sanitized ) ) {
            return new WP_Error(
                "app_expert_invalid_email",
                __( "Please provide a valid email address.", 'app-expert' ),
                array(
                    'status' => 400,
                )
            );
        }

        if ( !email_exists( $user_email_sanitized ) ) {
            return new WP_Error(
                "app_expert_invalid_email",
                __( "There is no account with that email address.", 'app-expert' ),
                array(
                    'status' => 400,
                )
            );
        }

        return true;
    }

}