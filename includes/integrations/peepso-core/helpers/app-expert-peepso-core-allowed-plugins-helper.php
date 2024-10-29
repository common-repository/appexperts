<?php
/**
 * ======================
 * Author : Marina Wagih.
 * Date :2/2/20.
 * ======================
 */
class App_Expert_Peepso_Core_Allowed_Plugins_Helper
{

    public static function isActive($pluginName)
    {
        return in_array($pluginName, (array)get_option('active_plugins', array()), true) || self::is_plugin_active_for_network($pluginName);
    }

    public static function is_plugin_active_for_network($plugin)
    {
        if (!is_multisite()) {
            return false;
        }

        $plugins = get_site_option('active_sitewide_plugins');
        if (isset($plugins[$plugin])) {
            return true;
        }

        return false;
    }

    public static function is_admin()
    {
        $user_id = get_current_user_id();
        if (user_can($user_id, 'manage_options')) {
            return TRUE;
        }

        $PeepSoUser = PeepSoUser::get_instance($user_id);
        $role = $PeepSoUser->get_user_role();
        if ('admin' === $role) {
            return TRUE;
        }
        return FALSE;
    }

    public static function can_pin($post_id)
    {

        if (self::is_admin()) {
            return TRUE;
        }

        if (!get_current_user_id()) {
            return FALSE;
        }

        return apply_filters('peepso_can_pin', FALSE, $post_id);
    }
}