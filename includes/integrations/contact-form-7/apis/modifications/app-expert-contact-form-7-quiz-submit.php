<?php
class App_Expert_Contact_Form_7_Quiz_Submit {
    private $regex = "/\/contact-form-7\/v1\/contact-forms\/[0-9]+\/feedback$/";

    public function __construct(){
        add_filter( 'wpcf7_validate_quiz', array($this, 'validate_from_api'), 2, 2 );
    }
    public function validate_from_api($result, $tag){
        $matches = [];
        $current_route = $_SERVER["REQUEST_URI"];
        $current_route = explode("?",$current_route);
        $current_route = $current_route[0];

        preg_match($this->regex,$current_route,$matches);
        if(count($matches)){
            $name = $tag->name;
            if(!isset($_POST["{$name}_i"]))return $result;

            if(!isset( $_POST['_wpcf7_quiz_answer_' . $name] )){
                $pipes = $tag->pipes;
                if ( $pipes instanceof WPCF7_Pipes and ! $pipes->zero() ) {
                    $used_pipe = $pipes->to_array();
                    $pipe = $used_pipe[$_POST["{$name}_i"]];
                    $answer = wp_unslash($pipe[1]);//[QUESTION,ANSWER]
                    $answer = wpcf7_canonicalize( $answer, array(
                        'strip_separators' => true,
                    ) );
                    $_POST['_wpcf7_quiz_answer_' . $name] = wp_hash($answer, 'wpcf7_quiz');
                }
            }
        }
        return $result;
    }

}
new App_Expert_Contact_Form_7_Quiz_Submit();