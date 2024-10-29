<?php
class App_Expert_register_user{
   public function __construct(){
       //todo: to be from app-expert register only & it's working
       add_action('rest_insert_user',array($this,'register_user'),99,3);
    }
    public function register_user($user, $request, $created )
    {
        if(!$created) return;
        $input = new PeepSoInput();
        $u = PeepSoUser::get_instance(0);

        $uname = $input->value('username', '', FALSE); // SQL Safe
        $email = $input->value('email', '', FALSE); // SQL Safe
        $passw = $input->raw('password', '');
        $pass2 = $input->raw('password_confirmation', '');

        $task = $input->value('task', '', FALSE); // SQL Safe

        $register = PeepSoRegister::get_instance();
        $register_form = $register->register_form();
        $form = PeepSoForm::get_instance();
        $form->add_fields($register_form['fields']);
        $form->map_request();
        $status = true;
        $_err_message = "";
        if (FALSE === $form->validate()) {
            $_err_message = __('Form contents are invalid.', 'peepso-core');
            $status = false;
        }

        // verify form contents
        if ('-register-save' != $task) {
            $_err_message = __('Form contents are invalid.', 'peepso-core');
            $status = FALSE;
        }

        if (empty($uname) || empty($email) || empty($passw)) {
            $_err_message = __('Required form fields are missing.', 'peepso-core');
            $status = FALSE;
        }

        $valid_email = apply_filters('peepso_register_valid_email', TRUE, $email);
        if (!is_email($email) || !$valid_email) {
            $_err_message = __('Please enter a valid email address.', 'peepso-core');
            $status = FALSE;
        }

        $id = get_user_by('email', $email);
        if (FALSE !== $id) {
            $_err_message = __('That email address is already in use.', 'peepso-core');
            $status = FALSE;
        }

        $id = get_user_by('login', $uname);
        if (FALSE !== $id) {
            $_err_message = __('That user name is already in use.', 'peepso-core');
            $status = FALSE;
        }

        if (PeepSo::get_option('registration_confirm_email_field', 1)) {
            $email_verify = $input->value('email_verify', '', FALSE); // SQL Safe
            if ($email !== $email_verify) {
                $_err_message = __('The emails you submitted do not match.', 'peepso-core');
                $status = FALSE;
            }
        }

        if ($passw != $pass2) {
            $_err_message = __('The passwords you submitted do not match.', 'peepso-core');
            $status = FALSE;
        }

        // checking additional fields is include in registration page?.
        if(isset($register_form['fields']['extended_profile_fields'])) {
            $valid_ext_fields = apply_filters('peepso_register_valid_extended_fields', TRUE, $input);
            if( FALSE === $valid_ext_fields) {
                $_err_message = __('Additional fields are invalid.', 'peepso-core');
                $status = FALSE;
            }
        }

        // Verify Invisible reCAPTCHA parameter if config is enabled.
        if (PeepSo::get_option('site_registration_recaptcha_enable', 0)) {
            $args = array(
                'body' => array(
                    'response' => $input->value('g-recaptcha-response', '', FALSE), // SQL Safe
                    'secret' => PeepSo::get_option('site_registration_recaptcha_secretkey', 0),
                )
            );
            $request = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', $args);
            $result = json_decode(wp_remote_retrieve_body($request), true);
            if (!$result) {
                $_err_message = __('Failed to verify reCAPTCHA.', 'peepso-core');
                $status = FALSE;
            }
            if (!$result['success']) {
                $_err_message = __('The reCAPTCHA code is invalid.', 'peepso-core');
                $status = FALSE;
            }
        }

        $wpuser = $u->create_user('', '', $uname, $email, $passw, '');
        $user = PeepSoUser::get_instance($wpuser);

        if (PeepSo::get_option('registration_disable_email_verification', '0')) {
            if (PeepSo::get_option('site_registration_enableverification', '0')) {
                $this->admin_approval($user);
            } else {
                $user->set_user_role('member');
            }
        } else {
            $user->set_user_role(apply_filters('peepso_user_default_role', 'register'));
        }
        do_action('peepso_register_new_user', $wpuser);
        return ['status'=>$status,'Error'=>$_err_message];
    }
}