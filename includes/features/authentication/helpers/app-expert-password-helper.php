<?php
class App_Expert_Password_Helper{
    public static function general_password_check($password)
    {

        if (empty($password)) {
            return new WP_Error('rest_user_invalid_password', __('Passwords cannot be empty.', 'app-expert'), array('status' => 400));
        }

        if (false !== strpos($password, '\\')) {
            return new WP_Error('rest_user_invalid_password', __('Passwords cannot contain the "\\" character.', 'app-expert'), array('status' => 400));
        }

        if (strlen($password) < 6) {
            return new WP_Error(
                'app_expert_password_short',
                __('Password is too short.', 'app-expert'),
                array('status' => 400)
            );
        }

        return $password;
    }
}