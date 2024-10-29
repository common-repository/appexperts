<?php
class App_Expert_Admin_Menu_Notifications_Page{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action( 'init',  array( $this, 'add_notification_pages_cpt' ));
    }
    public function add_settings_page()
    {
        $server_key = get_option('server_key');
        if(!empty($server_key)) {
            add_submenu_page('app_expert', __('Notifications', 'app-expert'), __('Notifications', 'app-expert'), 'manage_options', 'edit.php?post_type=as_notifications');
        }    
    }

    public function add_notification_pages_cpt() {
        $labels = array(
            'name' => _x('Notifications', 'Notifications',  'app-expert'),
            'singular_name' => _x(' Notification', 'Notifications',  'app-expert'),
            'menu_name' => __(' Notifications',  'app-expert'),
            'parent_item_colon' => __('Parent  Notification',  'app-expert'),
            'all_items' => __('All  Notifications',  'app-expert'),
            'view_item' => __('View  Notification',  'app-expert'),
            'add_new_item' => __('Add New  Notification',  'app-expert'),
            'add_new' => __('Add New',  'app-expert'),
            'edit_item' => __('Edit  Notification',  'app-expert'),
            'update_item' => __('Update  Notification',  'app-expert'),
            'search_items' => __('Search  Notification',  'app-expert'),
            'not_found' => __('Not Found',  'app-expert'),
            'not_found_in_trash' => __('Not found in Trash',  'app-expert'),
        );
        $args = array(
            'rewrite' => array('slug' => ' as_notifications'),
            'label' => __(' Notifications',  'app-expert'),
            'description' => __(' Notifications',  'app-expert'),
            'labels' => $labels,
            'supports' => array('title'),
            'public' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'show_in_nav_menus' => false,
            'show_in_admin_bar' => false,
            'can_export' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => true,
            'menu_position' => 100,
            'menu_icon' => '',
            'capability_type' => 'post',
            'capabilities' => array(
                'create_posts' => false, // Removes support for the "Add New" function ( use 'do_not_allow' instead of false for multisite set ups )
            ),
            'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts

        );
        register_post_type('as_notifications', $args);
    }
   
}
