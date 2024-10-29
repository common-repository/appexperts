<?php
/*
Plugin Name: AppExperts
Description: Integrate App Experts builder functionality with your wordpress website
Version: 1.4.3
Author: AppExperts
Author URI: https://appexperts.io/
License: GPLv2 or later
Text Domain: app-expert
*/
if (!defined('ABSPATH')) {
	exit();
}

define('APP_EXPERT_PATH',   plugin_dir_path(__FILE__));
define('APP_EXPERT_URL',  plugin_dir_url(__FILE__));
/**
 * Plugin Conventions:
 *--------------------
 *  - Files &dirs Name:
 *      - Dir Name  :
 *              - all letters should be  small .
 *              - should be separated with "-" for each word.
 *              - should be descriptive of it's content.
 *      - File Name :
 *              - all letters should be  small .
 *              - should be starting with "app-expert-".
 *              - should be descriptive of it's content.
 *  - Classes:
 *      - Naming :
 *              - should be separated with "_" for each word.
 *              - First letter  should be Capital for each word.
 *              - should be starting with "App_Expert_".
 *      - Purpose :
 *              - should HAVE SINGLE Responsibility.
 *  - Methods & variables :
 *      - Naming :
 *              - should be separated with "_" for each word.
 *              - all letters should be  small.
 *  - Constants
 *      - Naming :
 *              - should be separated with "_" for each word.
 *              - all letters should be  Capital.
 *-----------------------------------------------------------
 *  Plugin Main Dirs:
 *  - freemius => freemius sdk.
 *  - vendor   => composer packages files
 *  - includes
 *      - init => Plugin init
 *      - features => custom work for app-experts
 *          - {feature name}
 *              - database
 *              - assets
 *              - backend
 *              - apis
 *                  - endpoints
 *                  - middlewares
 *                  - modifications
 *                  - routes
 *                  - requests
 *              - frontend
 *              - helpers
 *              - templates
 *              - app-experts-{feature name}-init.php
 *      - integrations => other plugins changes/implementations
 *           - {integration name}
 *              - database
 *              - assets
 *              - backend
 *              - apis
 *                  - endpoints
 *                  - middlewares
 *                  - serializers
 *                  - modifications
 *                  - routes
 *                  - requests
 *              - frontend
 *              - helpers
 *              - templates
 *              - app-experts-{integration name}-init.php
 *
 **/

//todo:find a better way

const APP_EXPERT_PLUGIN_VERSION = '1.4.3';  // used in assets
const APP_EXPERT_API_NAMESPACE = 'app-expert/v1';

const APP_EXPERT_FILE = __FILE__;

const PAGE_RENDERER_SLUG = 'app-experts-page-renderer';
const MOBILE_CHECKOUT_PAGE_SLUG = 'mobile-app-checkout-page';
const MOBILE_THANK_YOU_PAGE_SLUG = 'mobile-app-woocommerce-thank-you';

const REMOVE_POST_TYPES = array(
    'wp_block',
    'attachment',
    'page'
);

const ACCESS_TOKEN_EXPIRATION_IN_DAYS = 7;
const REFRESH_TOKEN_EXPIRATION_IN_DAYS = 30;

const USER_IMAGE_SMALL = 200;
const USER_IMAGE_MEDIUM = 350;
const USER_IMAGE_LARGE = 650;

const NOTIFICATION_TABLE = 'push_notifications';
const USER_NOTIFICATION_TABLE = 'push_notifications_user';
const NOTIFICATION_TOKEN_META = '_as_notification_tokens';

const YITH_API_NAMESPACE = 'yith/v1';
const PEEPSO_CORE_API_NAMESPACE = 'peepso/v1';

const MOBILE_WPFORMS_PAGE_SLUG = 'ae-wpforms';
const WPFORMS_API_NAMESPACE = 'wpforms/v1';

require_once APP_EXPERT_PATH."includes/init/app-expert-init.php";