<?php
class App_Expert_Tag_Meta_Data    {

    public function support_listing()
    {
        return true;
    }


    /**
     * @var App_Expert_Term_Meta_Data
     */
    private $app_expert_term_meta_data;

    public function __construct() {

        $this->app_expert_term_meta_data = new App_Expert_Term_Meta_Data();
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public  function attach_data(array &$data){
        if (!isset($data['tags']) || empty($data['tags']))
            return;


        $tags = $this->app_expert_term_meta_data->get_term_data('post_tag' , $data['tags']);

        $data['tags'] = $tags;
    }

}