<?php

class App_Expert_Api_jwt_Token_Helper {

		private $user;

		private $issued_at;
        private static $auth_header="Authorization";

		public function __construct(WP_User $user)
		{
			$this->issued_at = time();
			$this->user = $user;
		}

		/**
		 * @return array
		 */
		public function get_access_token()
		{
			$args = array(
				'exp' => $this->get_expiration_date(true),
				'data' => array(
					'user' => array(
						'id' => $this->user->data->ID,
						'user_login' => $this->user->data->user_login,
						'user_uri' => property_exists($this->user->data, 'user_uri') ? $this->user->data->user_uri : null,
						'first_name' => property_exists($this->user->data, 'first_name') ? $this->user->data->first_name : null,
						'last_name' => property_exists($this->user->data, 'last_name')  ? $this->user->data->last_name : null,
						'user_email' => $this->user->data->user_email,
						'user_nicename' => $this->user->data->user_nicename,
						'user_display_name' => $this->user->data->display_name,
					)
				)
			);

			return array_merge($args, $this->get_static_data());
		}

		/**
		 * @return array
		 */
		public function get_refresh_token()
		{
			$args = array(
				'exp' => $this->get_expiration_date(false),
				'random' => rand()

			);
			return array_merge($args, $this->get_static_data());
		}

		/**
		 * @return array
		 */
		private function get_static_data()
		{
			return array(
				'iss' => get_bloginfo('name'),
				'iat' => $this->issued_at,
			);
		}

		/**
		 * @param bool $is_access_token
		 *
		 * @return float|int
		 */
		private function get_expiration_date(bool $is_access_token)
		{
			$days = $is_access_token ? ACCESS_TOKEN_EXPIRATION_IN_DAYS : REFRESH_TOKEN_EXPIRATION_IN_DAYS;
			return $this->issued_at + (DAY_IN_SECONDS * $days);
		}
        public static function getallheaders() {
            $headers = [];
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
                }
            }
            return $headers;
        }

        public static function get_user_from_token()
        {
            $user_id = 0;
            $headers=self::getallheaders();
            if(empty($headers[self::$auth_header])) return $user_id;

            $authorization_header =$headers[self::$auth_header];
            $token_auth_result = self::authenticate_user_token($authorization_header);
            if (!is_wp_error($token_auth_result)) {
                //append user id to request so that u don't need to decode the token again to get id
                $user_id = $token_auth_result;
            }
            return $user_id;
        }

        public static function authenticate_user_token($authorization_header)
        {

            if (empty($authorization_header)) {
                return new WP_Error(
                    'app_expert_no_authorization_header',
                    __('Authorization header must be provided', 'app-expert'),
                    array(
                        'status' => 401,
                    )
                );
            }

            if (!self::is_authorization_header_valid($authorization_header)) {
                return new WP_Error(
                    'app_expert_authorization_header_invalid',
                    __('Authorization header format is incorrect', 'app-expert'),
                    array(
                        'status' => 401,
                    )
                );
            }

            $token = explode(' ', $authorization_header)[1];

            $jwt_helper = new App_Expert_JWT_Helper();
            $decode_result = $jwt_helper->decode_token($token);

            if (is_a($decode_result, 'Exception')) {
                if(($decode_result->getMessage() == "Expired token") && function_exists('tribe_callback')){
                    remove_filter( 'determine_current_user', tribe_callback( 'promoter.connector', 'authenticate_user_with_connector') );
                }
                return new WP_Error(
                    $decode_result->getMessage(),
                    __('Something happened while decoding the token', 'app-expert'),
                    array(
                        'status' => 401,
                    )
                );
            }

            return $jwt_helper->get_user_id($decode_result);
        }
        /**
         * @param string $authorization_header
         *
         * @return bool
         */
        public static function is_authorization_header_valid(string  $authorization_header)
        {

            $bearer_token = explode(' ', $authorization_header);

            return count($bearer_token) === 2 && $bearer_token[0] === 'Bearer';
        }

        public static function is_auth(WP_REST_Request $request)
        {
            $header=$request->get_header(self::$auth_header);

            $has_bearer = str_contains($header,"Bearer");
            $user_id = get_current_user_id();
            if (!$has_bearer||empty($user_id)){
                return new WP_Error(
                    'app_expert_no_user_id',
                    __( 'No User ID was found.' , 'app-expert' ),
                    array( 'status' => 401 ) );
            }
            return true;
        }

}

