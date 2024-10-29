<?php
class App_Expert_Register_Endpoint extends WP_REST_Users_Controller{
    public function create_item($request)
    {
        $request = App_Expert_Profile_Helper::remove_user_meta_data_register($request);
        $username = $request->get_param('username');

        if (empty($username)) {
            $username = $this->get_generated_username($request);
            $request->set_param('username', $username);
        }

        $response =  parent::create_item($request);
        if (is_wp_error($response)) {
            return $this->handle_error_response($response);
        }

        $user     = get_user_by("ID",$response->data['id']);
        $response = App_Expert_User_Response_Helper::jwt_response($user,$request);

        if (is_wp_error($response))
            return $response;

        $response['code'] = 'app_expert_created_user';
        $response['message'] = 'User Created';

        return $response;
    }
    private function get_generated_username(WP_REST_Request $request, $suffix = '')
    {
        $email = $request->get_param('email');

        $email_parts = explode('@', $email);

        $email_username = $email_parts[0];

        // Exclude common prefixes.
        if (in_array(
            $email_username,
            array(
                'sales',
                'hello',
                'mail',
                'contact',
                'info',
            ),
            true
        )) {
            // Get the domain part.
            $email_username = $email_parts[1];
        }

        $username_parts[] = sanitize_user($email_username, true);

        $username = strtolower(implode('.', $username_parts));

        if ($suffix) {
            $username .= $suffix;
        }

        $illegal_logins = (array) apply_filters('illegal_user_logins', array());

        if (in_array(strtolower($username), array_map('strtolower', $illegal_logins), true)) {

            return $this->get_generated_username($request, '-' . zeroise(wp_rand(0, 9999), 4));
        }

        if (username_exists($username)) {
            $suffix = '-' . zeroise(wp_rand(0, 9999), 4);
            return $this->get_generated_username($request, $suffix);
        }

        return $username;
    }

    private function handle_error_response(WP_Error $response)
    {
        if (
            property_exists($response, 'errors')
            && isset($response->errors['existing_user_email'])
        ) {
            return new WP_Error(
                'existing_user_email',
                __('This email is already used.', 'app-expert'),
                array(
                    'status' => 400,
                    'params' => array(
                        'email' => __('This email is already used.', 'app-expert')
                    )
                )
            );
        }

        return $response;
    }
}