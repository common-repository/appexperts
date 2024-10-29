<?php
class App_Expert_Photo_Post_Apis{
   public function __construct(){
       add_filter('ae_peepso_add_post_parameters',array($this,'add_files_parameter'),10,1);
    }

    public function add_files_parameter($parameters)
    {
        $parameters['files' ] =  array(
            'description' => __( 'required if type is photo.' , 'app-expert' ),
            'type'        => 'Array',
            'required' => false
        );
        return $parameters; 
    }

}
new App_Expert_Photo_Post_Apis();