<?php 
/**
* ======================
* Author : Mahmoud Ramadan. 
* Date :6/7/21.    
* ======================
*/
class App_Expert_Category_Serializer {

    protected $category;
    protected $categoryKeys=[
        "id", "author_id", "name", "description", "groups_count"
    ];

    public function __construct($category){
        $this->category=$category;
    }
    public function get( ){
        
        $Arr=[];
        foreach($this->categoryKeys as $key){
            $Arr[$key]= $this->category->$key;
        }
       
        return $Arr;
    }
}