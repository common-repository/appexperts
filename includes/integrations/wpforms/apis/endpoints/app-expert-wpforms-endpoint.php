<?php
use WPML\TM\ATE\API\FingerprintGenerator;
use  WPML\PB\Helper;
class App_Expert_WPForms_Endpoint{
  
    public function get_all_forms(WP_REST_Request $request){
        $forms = wpforms()->get( 'form' )->get( '', [ 'order' => 'DESC' ] );
        $forms = ! empty( $forms ) ? $forms : [];
        $forms_data=[];
        if($forms){
            $forms_data = array_map(
                static function( $form ) {
                    $data['id']=$form->ID;
                    $data['post_title'] = htmlspecialchars_decode( $form->post_title, ENT_QUOTES );    
                    return $data;
                },
                $forms
            );
        }
        
        return App_Expert_Response::success('ae_wpforms_forms_retrieved','All forms Retrieved', ['forms'=>$forms_data]);
   }

      
}
