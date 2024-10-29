<?php

require(APP_EXPERT_PATH.'vendor/firebase/php-jwt/src/JWT.php');
require(APP_EXPERT_PATH.'vendor/firebase/php-jwt/src/Key.php');

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class App_Expert_JWT_Helper {

    /**
     * @var string
     */
    private $app_secret_key;

    public function __construct() {

        $settings = get_option('app_expert_settings');

        $this->app_secret_key = isset($settings['secret_key']) && !empty($settings['secret_key']) ? $settings['secret_key'] : '';
    }

    /**
     * @param WP_User $user
     *
     * @return array|Exception
     */
    public function get_jwt_token(WP_User $user) {

        if (!$this->is_app_secret_valid()) return $this->get_app_secret_invalid_exception();

        $token_handler = new App_Expert_Api_jwt_Token_Helper($user);

        $token = array(
            'access_token' => JWT::encode($token_handler->get_access_token() , $this->app_secret_key, 'HS256'),
            'refresh_token' => $this->encode_refresh_token($token_handler->get_refresh_token())
        );

        $this->save_refresh_token($token , $user);

        return $token;

    }

    /**
     * @param string $token
     *
     *
     * @return Exception|object|string
     */
    public function decode_token(string $token ){
        if (!$this->is_app_secret_valid())
            return $this->get_app_secret_invalid_exception();


        try {
            $token = JWT::decode($token, new Key($this->app_secret_key, 'HS256'));

            if (!$this->is_issuer_valid($token)){
                return new Exception('app_expert_invalid_issuer' , 401);
            }

            if (!$this->is_token_date_valid($token)){
                return new Exception('app_expert_expired_token' , 401);
            }


            if (!$this->is_access_token_data_valid($token)){
                return new Exception('app_expert_data_invalid_use_access_token' , 401);
            }

            return $token;

        }catch (Exception $exception){

            return $exception;
        }
    }


    /**
     * @return bool
     */
    private function is_app_secret_valid(){
        return isset($this->app_secret_key) && !empty($this->app_secret_key);
    }

    /**
     * @return Exception
     */
    private function get_app_secret_invalid_exception(){
        return new Exception('app_expert_no_secret_key' , 401);

    }


    /**
     * @param array $refresh_token
     *
     * @return string
     */
    public function encode_refresh_token(array $refresh_token){
        return JWT::urlsafeB64Encode(JWT::jsonEncode($refresh_token));
    }

    /**
     * @param string $refresh_token
     *
     * @return Exception|object
     */
    public function decode_refresh_token(string $refresh_token){
        try {
            $token = JWT::jsonDecode( JWT::urlsafeB64Decode( $refresh_token ) );

            if (!$this->is_issuer_valid($token)){
                return new Exception('app_expert_invalid_issuer' , 401);
            }

            if (!$this->is_token_date_valid($token)){
                return new Exception('app_expert_expired_token' , 401);
            }

            return $token;
        }catch (Exception $exception){

            return $exception;
        }
    }

    /**
     * @param string $refresh_token
     *
     * @return bool
     */
    public function is_refresh_token_valid(string  $refresh_token) : bool {

        $users = $this->get_user_by_refresh_token($refresh_token);

        return isset($users[0]) && !empty($users[0])
                && count($users) === 1;
    }

    /**
     * @param string $refresh_token
     *
     * @return array
     */
    public function get_user_by_refresh_token(string $refresh_token){
        return get_users(
            array(
                'meta_key'=> 'app_expert_refresh_token',
                'meta_value' => $refresh_token,
                'meta_compare'=> 'LIKE'
            ));
    }

    /**
     * @param stdClass $token
     *
     * @return int
     */
    public function get_user_id(stdClass $token){
        return $token->data->user->id;
    }


    /**
     * @param array $token
     * @param WP_User $user
     */
    private function save_refresh_token(array $token , WP_User $user){
        $old_token = get_user_meta($user->ID, 'app_expert_refresh_token', true);

        $typeof_refresh_token = gettype($old_token);
        if ($typeof_refresh_token == 'string'){
            $old_token = array($old_token);
        }
        $old_token[]=$token['refresh_token'];
        update_user_meta($user->ID, 'app_expert_refresh_token', $old_token);
    }

    /**
     * @param stdClass $token
     *
     * @return bool
     */
    private function is_issuer_valid(stdClass $token){
        return isset($token->iss) && !empty($token->iss)
            && $token->iss === get_bloginfo('name');
    }

    /**
     * @param stdClass $token
     *
     * @return bool
     */
    private function is_token_date_valid(stdClass $token){
        return isset($token->exp) && !empty($token->exp)
               && $token->exp > time();
    }

    /**
     * @param stdClass $token
     *
     * @return bool
     */
    private function is_access_token_data_valid(stdClass $token){
        return isset($token->data->user->user_email) && !empty($token->data->user->user_email);
    }



}
