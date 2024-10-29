<?php
class App_Expert_Settings_Endpoint extends WP_REST_Post_Types_Controller {

        public function validate_license_key( WP_REST_Request $request ) {
            $licenseKey = $request->get_param( 'license_key' );
            $licenseKeysOption = json_decode(get_option('license_key'));
            $isValid = false;
            foreach ($licenseKeysOption as $licenseKeyOption) {
                if($licenseKeyOption->key === $licenseKey){
                    $isValid = true;
                    break;
                }
            }

            if(!$isValid){
                return new WP_Error(
                    'app_expert_license_key_not_valid',
                    __( 'License key is not valid', 'app-expert' ),
                    array(
                        'status' => 422,
                        'isValid' => $isValid
                    )
                );
            }
            $response['code'] = 'app_expert_license_key_valid';
            $response['message'] = 'License key valid';
            $response['data'] = [
                'status' => 200,
                'isValid' => $isValid
            ];
            return $response;
        }

        public function save_settings( WP_REST_Request $request ) {

            $licenseKey = $request->get_param( 'license_key' );

            $licenseKeysOption = json_decode(get_option('license_key'));
            $isValid = false;
            foreach ($licenseKeysOption as $licenseKeyOption) {
                if($licenseKeyOption->key === $licenseKey){
                    $isValid = true;
                    break;
                }
            }
            if(!$isValid) return new WP_Error(
                    'app_expert_license_key_not_valid',
                    __( 'License key is not valid', 'app-expert' ),
                    array(
                        'status' => 422,
                    )
                );

            //todo: in v2 add multiple error or general msg
            $app_token = $request->get_param( 'app_token' );
            if(!empty($app_token)){
                update_option( 'app_token', $app_token);
                $response['code'] = 'app_token_is_updated';
                $response['message'] = 'App Token is updated successfully';
            }else{
                delete_option( 'app_token' );
                $response['code'] = 'app_token_is_removed';
                $response['message'] = 'App Token is removed successfully';
            }
            $api_key = $request->get_param( 'api_key' );
            if(!empty($api_key)){
                update_option( 'api_key', $api_key);
                $response['code'] = 'api_key_is_updated';
                $response['message'] = 'Api Key is updated successfully';
            }else{
                delete_option( 'api_key' );
                $response['code'] = 'api_key_is_removed';
                $response['message'] = 'Api Key is removed successfully';
            }
            $response['data'] = [
                'status' => 200,
            ];

            return apply_filters("ae_save_settings_response",$response,$request);

        }

        public function get_app_token() {
            $app_token=get_option( 'app_token',"" );
            if(!empty($app_token)){
                $response['code'] = "app_token_is_found";
                $response['message'] = "App Token is found";
                $response['data'] = [
                    'status' =>200,
                    'app_token' => $app_token
                ];
            }else{
                $response['code'] = "app_token_is_not_found";
                $response['message'] = "App Token is Not found";
                $response['data'] = [
                    'status' =>401,
                    'app_token' => $app_token
                ];
            }

            return $response;
        }

        private function get_posts_settings($request){
            $post_types=[];
            $locales =App_Expert_Language::get_active_languages();
            foreach (get_post_types(array(), 'object') as $obj) {
                if (empty($obj->show_in_rest) || array_search($obj->name, REMOVE_POST_TYPES, true) !== FALSE || ('edit' === $request['context'] && !current_user_can($obj->cap->edit_posts))) {
                    continue;
                }

                $post_type = $this->prepare_item_for_response($obj, $request);
                if (is_null($post_type)) {
                    continue; // Some required data are missing
                }

                $translated_names = [];
                foreach ($locales as $locale) {
                    $translation_domain = $this->get_post_type_translation_domain($post_type->data['slug']);
                    $translated_names[$locale['code']] = apply_filters('ae_post_name_translation',
                        __($post_type->data['name'], $translation_domain),
                        $post_type,
                        $translation_domain,
                        $locale['code']);
                }
                $post_type->data['translated_name'] = $translated_names;
                $post_types[$obj->name] = $this->prepare_response_for_collection($post_type);
            }
            return $post_types;
        }
        private function get_discussion_settings(){
            return [
                //"default_post_settings"
                "default_pingback_flag"=>[
                    "description" => "Attempt to notify any blogs linked to from the post",
                    "value"       => get_option('default_pingback_flag',"no"),
                ],
                "default_ping_status"=>[
                    "description" => "Allow link notifications from other blogs (pingbacks and trackbacks) on new posts",
                    "value"       => get_option('default_ping_status',"no"),
                ],
                "default_comment_status"=>[
                    "description" => " Allow people to submit comments on new posts. Individual posts may override these settings. Changes here will only be applied to new posts.",
                    "value"       => get_option('default_comment_status',"no"),
                ],
                //"other_comment_settings"
                "require_name_email"=>[
                    "description" => "Comment author must fill out name and email",
                    "value"       => get_option('require_name_email',"no"),
                ],
                "comment_registration"=>[
                    "description" => "Users must be registered and logged in to comment",
                    "value"       => get_option('comment_registration',"no"),
                ],
                "close_comments_for_old_posts"=>[
                    "description" => "Automatically close comments on posts",
                    "value"       => get_option('close_comments_for_old_posts',"no"),
                ],
                "close_comments_days_old"=>[
                    "description" => "Number of days to Automatically close comments on posts that older",
                    "value"       => get_option('close_comments_days_old',"14"),
                ],
                "show_comments_cookies_opt_in"=>[
                    "description" => "Show comments cookies opt-in checkbox, allowing comment author cookies to be set",
                    "value"       => get_option('show_comments_cookies_opt_in',"no"),
                ],
                "thread_comments"=>[
                    "description" => "Enable threaded (nested) comments",
                    "value"       => get_option('thread_comments',"no"),
                ],
                "thread_comments_depth"=>[
                    "description" => "Depth Level for threaded (nested) comments",
                    "value"       => get_option('thread_comments_depth',"5"),
                ],
                "page_comments"=>[
                    "description" => "Break comments into pages",
                    "value"       => get_option('page_comments',"no"),
                ],
                "comments_per_page"=>[
                    "description" => "number of comments in page",
                    "value"       => get_option('comments_per_page',"50"),
                ],
                "default_comments_page"=>[
                    "description" => "sort of the comments' pages",
                    "value"       => get_option('default_comments_page',"newest"),
                ],
                "comment_order"=>[
                    "description" => "sort of the comments",
                    "value"       => get_option('comment_order','asc'),
                ],
                //"before_a_comment_appears"
                "comment_moderation"=>[
                    "description" => "Comment must be manually approved",
                    "value"       => get_option('comment_moderation',"no"),
                ],
                "comment_previously_approved"=>[
                    "description" => "Comment author must have a previously approved comment",
                    "value"       => get_option('comment_previously_approved',"no"),
                ],

            ];
        }

        public function get_items($request){
            $data = [
                'post_types' => $this->get_posts_settings($request),
                'discussion' => $this->get_discussion_settings()
            ];

            $data = apply_filters('ae_settings_data', $data);

            $response =  rest_ensure_response($data);

            if (is_wp_error($response))
                return $response;


            return App_Expert_Response::wp_list_success(
                $response,
                'app_expert_settings_retrieved',
                'retrieved all settings'
            );
        }
        /**
         * Get translation domain for the given post type
         * @var string $post_type Post type slug
         *
         * @return string domain name
         */
        protected function get_post_type_translation_domain($post_type)
        {
            switch ($post_type):
                case 'post':
                    $domain = 'default';
                    break;
                default:
                    $domain = 'admin_texts_cptui_post_types';
                    break;
            endswitch;
            $domain = apply_filters('ae_post_type_translation_domain', $domain, $post_type);
            return $domain;
        }
        /**
         * Prepares a post type object for serialization.
         *
         * @param WP_Post_Type    $post_type Post type object.
         * @param WP_REST_Request $request   Full details about the request.
         * @return WP_REST_Response Response object.
         */
        public function prepare_item_for_response($post_type, $request)
        {

            $fields = $this->get_fields_for_response($request);
            $data   = array();


            // Check all required keys exists
            $required_keys = ['name', 'slug', 'rest_base'];
            if (count(array_intersect($required_keys, array_keys($fields))) == count($required_keys)) {
                return null;
            }

            $data['name'] = $post_type->label;
            $data['slug'] = $post_type->name;
            $data['rest_base'] = $post_type->rest_base ? $post_type->rest_base : $post_type->name;
            $data['taxonomies'] = get_object_taxonomies($post_type->name);



            $context = !empty($request['context']) ? $request['context'] : 'view';
            $data    = $this->add_additional_fields_to_object($data, $request);
            $data    = $this->filter_response_by_context($data, $context);


            return rest_ensure_response($data);
        }

    }

