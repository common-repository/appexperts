<?php
class App_Expert_Edit_Profile_Request extends App_Expert_Auth_Request {

    public function rules(){
        return [
            'first_name' =>'check_first_name',
            'last_name' =>'check_last_name',
            'email'=>'check_email'
           ];
    }

    public function check_first_name(){
        $val = sanitize_text_field($this->request->get_param("first_name"));
        if (strlen($val) > 50) {
            return $this->get_validation_error("invalid_first_name_length", "first_name", __("Invalid parameter(s): first_name", "app-expert"), __("The first name field must not exceed 50 chatacters", "app-expert"));
        }
        return !!$val;
    }

    public function check_last_name(){
        $val =  sanitize_text_field($this->request->get_param("last_name"));
        if (strlen($val) > 50) {
            return $this->get_validation_error("invalid_last_name_length", "last_name", __("Invalid parameter(s): last_name", "app-expert"), __("The last name field must not exceed 50 chatacters", "app-expert"));
        }
        return !!$val;
    }

    public function check_email(){
        $email = $this->request->get_param("email");
        if (strlen($email) > 50) {
            return $this->get_validation_error("invalid_email_address_length", "email", __("Invalid parameter(s): email", "app-expert"), __("The email field must not exceed 50 chatacters", "app-expert"));
        }
        if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->get_validation_error("invalid_email_address_format", "email", __("Invalid parameter(s): email", "app-expert"), __("The email field must match email format", "app-expert"));
        }
        return true;
    }

    private function get_validation_error($key, $field, $message, $error_message)
    {
        return new WP_Error(
            $key,
            $message,
            array(
                'status' => 400,
                'params' => array(
                    $field => $error_message
                )
            )
        );
    }
}