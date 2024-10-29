<?php
class App_Expert_Contact_Form_7_Form_List{
    private  $route_all_regex = "/\/contact-form-7\/v1\/contact-forms(\/)?$/";
    private  $route_details_regex    = "/\/contact-form-7\/v1\/contact-forms\/[0-9]+(\/)?$/";
    public function __construct(){
        add_filter('rest_request_after_callbacks', array($this, 'modify_form_data'), 999, 3);
    }
    public function modify_form_data($response, array $handler, \WP_REST_Request $request) {
        if(!($response instanceof WP_REST_Response)) return $response;

        $current_route = $request->get_route();
        $matches=[];
        //list of forms
        preg_match($this->route_all_regex,$current_route,$matches);

        if(count($matches)){
           $forms = $response->get_data();
           foreach ($forms as $i=>$form){
               $response->data[$i] = $this->add_form_trabslations($form);
           }
           return $response;
        }
        preg_match($this->route_details_regex,$current_route,$matches);

        if(count($matches)){
            $response->data = $this->add_form_trabslations($response->get_data());
        }

        return $response;
    }

    private function add_form_trabslations($form)
    {
        $languages =App_Expert_Language::get_active_languages();
        $form['translations']=[];
        if ( !empty( $languages ) ) {
            foreach( $languages as $l ) {
                $form['translations'][$l['language_code']] = apply_filters( 'wpml_object_id', $form['id'], 'page', false, $l['language_code']);
            }
        }
        return $form;
    }
}
new App_Expert_Contact_Form_7_Form_List();