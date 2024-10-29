<?php
class App_Expert_Admin_Menu_Mobile_pages{

    private $_current_feature;
    public function __construct(App_Expert_Feature $_current_feature)
    {
        $this->_current_feature = $_current_feature;
        add_action('admin_menu', [$this, 'add_mobile_pages_page']);
        add_action( 'init',  array( $this, 'add_mobile_pages_cpt' ));

    }
    public function add_mobile_pages_cpt() {
        $labels = array(
            'name' => _x('Mobile pages', 'Mobile pages',  'app-expert'),
            'singular_name' => _x(' Mobile page', 'Mobile page',  'app-expert'),
            'menu_name' => __(' Mobile pages',  'app-expert'),
            'parent_item_colon' => __('Parent  Mobile page',  'app-expert'),
            'all_items' => __('All  Mobile pages',  'app-expert'),
            'view_item' => __('View  Mobile page',  'app-expert'),
            'add_new_item' => __('Add New  Mobile page',  'app-expert'),
            'add_new' => __('Add New',  'app-expert'),
            'edit_item' => __('Edit  Mobile page',  'app-expert'),
            'update_item' => __('Update  Mobile page',  'app-expert'),
            'search_items' => __('Search  Mobile page',  'app-expert'),
            'not_found' => __('Not Found',  'app-expert'),
            'not_found_in_trash' => __('Not found in Trash',  'app-expert'),
        );
        $args = array(
            'rewrite' => array('slug' => ' as_mobile_page'),
            'label' => __(' Mobile pages',  'app-expert'),
            'description' => __(' Mobile pages',  'app-expert'),
            'labels' => $labels,
            'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'page-attributes', 'custom-fields', 'comments', 'revisions' ),
            'public' => true,
            'has_archive' => true,
            'show_in_rest' => true,
            'hierarchical' => true,
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
            'map_meta_cap' => true, // Set to `false`, if users are not allowed to edit/delete existing posts

        );
        register_post_type('as_mobile_pages', $args);
    }
    public function add_mobile_pages_page()
    {
        add_submenu_page('app_expert',  __('Mobile pages', 'app-expert'), __('Mobile pages', 'app-expert'), 'manage_options', 'edit.php?post_type=as_mobile_pages');
    }
}
