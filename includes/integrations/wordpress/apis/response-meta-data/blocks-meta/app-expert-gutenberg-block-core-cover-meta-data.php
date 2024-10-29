<?php
class App_Expert_Gutenberg_Blocks_Core_Cover_Meta_Data{
    public function __construct(){
        add_filter('ae_blocks_core_cover',[$this,'attach_data'],10,2);
    }
    public  function attach_data($block,$post){
        $block['gebto'] = true;
        return $block;
    }
}
new App_Expert_Gutenberg_Blocks_Core_Cover_Meta_Data();