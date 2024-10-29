<?php
class App_Expert_Post_Translation_Meta_Data{

		public function __construct()
		{
            add_filter('ae_post_response',[$this,'attach_data'],10,2);
		}
        public  function attach_data(WP_REST_Response $response,WP_REST_Request $request){
            $data = $response->get_data();
            if (!isset($data['id']) || empty($data['id']))
                return $response;

            $language = isset($_GET['wpml_language']) ? sanitize_key($_GET['wpml_language']) : null;
            if (!$language) {
                return $response;
            }
            $type = $request->has_param('type') ?  $request->get_param('type') : "";
            $trans_id = apply_filters('wpml_object_id', $data['id'], $type, false, $language);
            if (!$trans_id || $data['id'] == $trans_id) {
                return $response;
            }

            // Override content with translated content
            $post = get_post($trans_id);
            if(!$post){
                return $response;
            }

            $data['id'] =  $trans_id;
            $data['title'] =  array_merge($data['title'], [
                'rendered' => $post->post_title
            ]);
            $data['content'] = array_merge($data['content'], [
                'rendered' => $post->post_content,

            ]);
            $data['excerpt'] = array_merge($data['excerpt'], [
                'rendered' => $post->post_excerpt,
            ]);
            $response->set_data($data);
            return $response;
        }
}
new App_Expert_Post_Translation_Meta_Data();