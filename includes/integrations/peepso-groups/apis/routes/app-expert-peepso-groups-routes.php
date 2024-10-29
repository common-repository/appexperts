<?php

class App_Expert_Peepso_Groups_Routes
{

    public function __construct(){
        $this->register_routes();
    }

    public function register_routes()
    {
        //todo: why list & inner without auth check?
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/groups/is-plugin-active', "App_Expert_Peepso_Groups_Endpoint@is_active" , array());
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/groups', "App_Expert_Peepso_Groups_Endpoint@get" , $this->get_groups_parameters());
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/groups/(?P<id>[\d]+)', "App_Expert_Peepso_Groups_Endpoint@get_one", $this->get_single_group_parameters());
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/groups/categories', "App_Expert_Peepso_Groups_Endpoint@get_categories", array(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/groups', "App_Expert_Peepso_Groups_Endpoint@add" , $this->add_groups_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/groups/change_avatar', "App_Expert_Peepso_Groups_Endpoint@change_avatar" , $this->change_avatar_groups_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/groups/change_cover', "App_Expert_Peepso_Groups_Endpoint@change_cover" , $this->change_cover_groups_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/groups/(?P<group_id>[\d]+)/members', "App_Expert_Peepso_Groups_Endpoint@group_members" , $this->get_members_groups_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/groups/change-settings', "App_Expert_Peepso_Groups_Endpoint@change_settings" , $this->change_settings_groups_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::get(PEEPSO_CORE_API_NAMESPACE, '/groups/(?P<group_id>[\d]+)/invite-list', "App_Expert_Peepso_Groups_Endpoint@invite" , $this->invite_list_groups_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/groups/action/invite', "App_Expert_Peepso_Groups_Endpoint@passive_invite" , $this->invite_groups_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/groups/action/(?P<gb_action>\D+)', "App_Expert_Peepso_Groups_Endpoint@actions" , $this->actions_groups_parameters(),"App_Expert_Auth_Request");
        App_Expert_Route::post(PEEPSO_CORE_API_NAMESPACE, '/groups/follow', "App_Expert_Peepso_Groups_Endpoint@follow" , $this->follow_groups_parameters(),"App_Expert_Auth_Request");
    }

    public function get_groups_parameters(){
        $parameters = array(
                'user_id'        => array(
                    'description' => __( 'groups list per user_id with default value 0' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'limit'        => array(
                    'description' => __( 'number of posts.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'page'        => array(
                    'description' => __( 'page number.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'orderby'        => array(
                    'description' => __( 'order by groups if no value parameter is set with settings value' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'sort'        => array(
                    'description' => __( 'groups list sorting and its default value is "desc"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'search'        => array(
                    'description' => __( 'txt to search group content' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'search_mode'        => array(
                    'description' => __( 'groups list search_ode and its default value is "exact"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'category'        => array(
                    'description' => __( 'groups list per category ith default value 0' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                )

            );
        return $parameters;
    }

    public function add_groups_parameters(){
        $parameters = array(
            'name'        => array(
                'description' => __( 'Group name.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'description'        => array(
                'description' => __( 'Group description.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'category_id'        => array(
                'description' => __( 'album group category_ids' , 'app-expert' ),
                'type'        => 'Array',
                'required' => false
            ),
            'privacy'        => array(
                'description' => __( 'Group privacy [public:0, members_only:1, only_me:2]' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )
        );
        return $parameters;

    }

    public function get_single_group_parameters(){
        return array(
            'id' => array(
                'description' => __( 'group id ', 'app-expert' ),
                'type'        => 'integer',
            )
        );
    }

    public function change_avatar_groups_parameters(){
        return  array(
            'group_id'        => array(
                'description' => __( 'group id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'filedata'        => array(
                'description' => __( 'group avatar image' , 'app-expert' ),
                'type'        => 'file',
                'required' => false
            )

        );

    }

    public function change_cover_groups_parameters(){
        return  array(
            'group_id'        => array(
                'description' => __( 'group id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'filedata'        => array(
                'description' => __( 'group cover image' , 'app-expert' ),
                'type'        => 'file',
                'required' => false
            )

        );

    }

    public function get_members_groups_parameters(){
        $parameters = array(
                'group_id'        => array(
                    'description' => __( 'group id.' , 'app-expert' ),
                    'type'        => 'integer',
                ),
                'limit'        => array(
                    'description' => __( 'number of posts.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'page'        => array(
                    'description' => __( 'page number.' , 'app-expert' ),
                    'type'        => 'integer',
                    'required' => false
                ),
                'order_by'        => array(
                    'description' => __( 'order by group members and its default value is "gm_joined"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'order'        => array(
                    'description' => __( 'groups list sorting and its default value is "desc"' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                ),
                'role'        => array(
                    'description' => __( 'value should be ""(for all members),"management" (management),"pending_admin"(for pending),"pending_user"(for invited),"banned" (for banned)' , 'app-expert' ),
                    'type'        => 'string',
                    'required' => false
                )

            );
        return $parameters;
    }

    public function change_settings_groups_parameters(){
        return  array(
            'group_id'        => array(
                'description' => __( 'group id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'name'        => array(
                'description' => __( 'Group name.' , 'app-expert' ),
                'type'        => 'string',
                'required' => true
            ),
            'description'        => array(
                'description' => __( 'Group description.' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'category_id'        => array(
                'description' => __( 'album group category_ids' , 'app-expert' ),
                'type'        => 'Array',
                'required' => false
            ),
            'properties'        => array(
                'description' => __( 'group properties is an array of objects, each object contains two keys "property_name" and "property_value" and its values' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        );

    }

    public function invite_list_groups_parameters(){
        return  array(
            'group_id'        => array(
                'description' => __( 'group id.' , 'app-expert' ),
                'type'        => 'integer',
            ),
            'limit'        => array(
                'description' => __( 'number of posts.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'page'        => array(
                'description' => __( 'page number.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'orderBy'        => array(
                'description' => __( 'order by groups if no value parameter is set with default value "real_name"' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'order'        => array(
                'description' => __( 'groups list sorting and its default value is "desc"' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            ),
            'query'        => array(
                'description' => __( 'search query' , 'app-expert' ),
                'type'        => 'string',
                'required' => false
            )
        );

    }

    public function invite_groups_parameters(){
        return  array(
            'group_id'        => array(
                'description' => __( 'group id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'user_id'        => array(
                'description' => __( 'user id to invite' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )

        );

    }

    public function follow_groups_parameters(){
        return  array(
            'group_id'        => array(
                'description' => __( 'group id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            ),
            'follow'        => array(
                'description' => __( 'follow or not. allowed value:(0,1). DO NOT SEND IT IF THE VALUE IS NOT UPDATED' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'notify'        => array(
                'description' => __( 'notify or not. allowed value:(0,1). DO NOT SEND IT IF THE VALUE IS NOT UPDATED' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            ),
            'email'        => array(
                'description' => __( 'send email or not. allowed value:(0,1). DO NOT SEND IT IF THE VALUE IS NOT UPDATED' , 'app-expert' ),
                'type'        => 'integer',
                'required' => false
            )

        );

    }

    public function actions_groups_parameters(){
        return array(
            'gb_action' => array(
                'description' => __( 'user id to follow ', 'app-expert' ),
                'type'        => 'string',
            ),
            'group_id'        => array(
                'description' => __( 'group id.' , 'app-expert' ),
                'type'        => 'integer',
                'required' => true
            )
        );
    }

}

new App_Expert_Peepso_Groups_Routes();

