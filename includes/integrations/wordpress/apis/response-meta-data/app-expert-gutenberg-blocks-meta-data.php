<?php
class App_Expert_Gutenberg_Blocks_Meta_Data{
    public function __construct(){
        add_filter('ae_single_page_response',[$this,'attach_data'],10,2);
    }
    public  function attach_data(WP_REST_Response $response,$request){
        $post   = get_post($response->data['id']);
        $blocks=  parse_blocks( $post->post_content );
        foreach ($blocks as $i=>$block)
        {
//string(26) "ae_blocks_core_gallery"
//string(26) "ae_blocks_core_heading"
//string(24) "ae_blocks_core_query"
            if(!empty($block["blockName"]))
                $blocks[$i] = apply_filters('ae_blocks'.str_replace("/","_",$block["blockName"]),$block,$post);

        }
        $response->data['blocks'] =$blocks;
        return $response;
    }

}
new App_Expert_Gutenberg_Blocks_Meta_Data();