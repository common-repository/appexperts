<?php

class App_Expert_Notification_Token_Endpoint{

        public function handle_register_token( WP_REST_Request $request ){
            $token = $request->get_param( 'token' );

            $user_id = get_current_user_id();

            $tokens = get_user_meta($user_id,NOTIFICATION_TOKEN_META,true);

            if(!is_array($tokens)) $tokens=[];
            if(!in_array($token,array_values($tokens))) $tokens[]=$token;
            update_user_meta($user_id,NOTIFICATION_TOKEN_META,$tokens);

            // add user role to topics
            $user_meta = get_userdata($user_id);
            $user_roles = $user_meta->roles;
            foreach ($user_roles as $role ){
                App_Expert_FCM_Helper::set_topics($role,$tokens);
            }

            return $this->get_response( __("token added",'app-expert' ));
        }
        public function handle_unregister_token( WP_REST_Request $request ){
            $token = $request->get_param( 'token' );

            $user_id=get_current_user_id();
            $tokens = get_user_meta($user_id,NOTIFICATION_TOKEN_META,true);

            if(!empty($tokens)){
                if (($key = array_search($token, $tokens)) !== false) {
                    unset($tokens[$key]);
                }
                update_user_meta($user_id,NOTIFICATION_TOKEN_META,$tokens);

                // remove user roles from topics
                $user_meta = get_userdata($user_id);
                $user_roles = $user_meta->roles;
                foreach ($user_roles as $role ){
                    App_Expert_FCM_Helper::set_topics($role,$tokens,false);
                }
            }
            

            return $this->get_response( __("token removed",'app-expert' ));
        }
        /**
         * @param WP_User $user
         * @param array $token
         *
         * @return array|WP_Error
         */
        private function get_response($msg){

            $response = New WP_REST_Response();

            $response->set_data(['message'=>$msg]);

            $response = rest_ensure_response($response);

            if (is_wp_error($response))
                return $response;
            return App_Expert_Response::wp_rest_success(
                $response,
                'app_expert_updated_notification_tokens',
                'Tokens updated'
            );
        }


    }

