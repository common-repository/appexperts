<h2 style="color: #31602b"><?php _e('App Experts Settings', 'app-expert'); ?> </h2>

<?php
$tabs = apply_filters('ae_settings_tabs',array());
$current=isset($_REQUEST["tab"])?$_REQUEST["tab"]:apply_filters('ae_default_settings_tab','license');
echo '<div id="icon-themes" class="icon32"><br></div>';
echo '<h2 class="nav-tab-wrapper">';
foreach( $tabs as $tab => $data ){
    $class = ( $tab == $current ) ? ' nav-tab-active' : "";
    $url=admin_url("admin.php?page=app_expert_settings&tab=$tab");
    echo "<a class='nav-tab$class' href='$url'>{$data['tab_name']}</a>";

}
echo '</h2>';

include_once $tabs[$current]['tab_view'];

wp_enqueue_script( 'copy_to_clipboard' );
wp_enqueue_script( 'app_expert_copy_to_clipboard' );
