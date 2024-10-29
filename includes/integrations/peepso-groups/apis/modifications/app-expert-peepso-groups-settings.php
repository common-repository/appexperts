<?php
//ae_settings_data
class App_Expert_Peepso_Groups_Settings{
    public function __construct() {
        add_filter( "ae_settings_data", array($this,'add_additional_params_to_settings'), 10, 1 );
    }
    public function add_additional_params_to_settings($data){

        $data["peepso"]['groups_creation_enabled']=(1==PeepSo::get_option('groups_creation_enabled', 1)?"1":"0");
        if(class_exists('PeepSoGroupPrivacy')){
            $data["peepso"]["group_privacy_settings"]=PeepSoGroupPrivacy::_();
            foreach ($data["peepso"]["group_privacy_settings"] as $key=>$values){
                //todo:add filter to extend
                $values['icon']= APP_EXPERT_URL."includes/integrations/peepso-groups/assets/imgs/privacy-group-icons/icon-{$values['id']}.svg";
                $data["peepso"]["group_privacy_settings"][$key]=$values;
            }
            $data["peepso"]["group_privacy_default"]= PeepSoGroupPrivacy::PRIVACY_OPEN;
        }
        else{
            $data["peepso"]["group_privacy_settings"]=[];
            $data["peepso"]["group_privacy_default"]=-1;
        }

        $data['peepso']['polls']['polls_group'] =  (string)PeepSo::get_option('polls_group', 0);


        $default_sorting = PeepSo::get_option('groups_default_sorting','id');
        $default_sorting_order = PeepSo::get_option('groups_default_sorting_order','DESC');

        $groups_categories_enabled = PeepSo::get_option('groups_categories_enabled', FALSE);

        $data["peepso"]['groups_filters']=[
            "default_orderby"=>$default_sorting,
            "default_sort"=>$default_sorting_order,
            "options"=>[
                [
                    "value"=>"id",
                    "label"=>__('Recently added', 'groupso')
                ],
                [
                    "value"=>"post_title",
                    "label"=>__('Alphabetical', 'groupso')
                ],
                [
                    "value"=>"meta_members_count",
                    "label"=>__('Members count', 'groupso')
                ],
            ]
        ];

        return $data;
    }

}
new App_Expert_Peepso_Groups_Settings();