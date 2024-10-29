<?php
/**
 * ==================================
 * Author : marina
 * Date :6/24/21.
 * ==================================
 **/


class App_Expert_Peepso_Groups_Action_Helper
{
    public static function getActions($actions){
        $arr=[];
        foreach ($actions as $action){
            if(is_array($action['action'])){
                $arr= array_merge($arr,$action['action']);
            }else{
                $arr[] = $action;
            }
        }
        return $arr;
    }
}