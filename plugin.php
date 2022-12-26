<?php
/**
 * @wordpress-plugin
 * Plugin Name:       Shabbat - Closed
 * Description:       This plugin will automaticly close the website for Shabbat between 17:00 and 18:00, and display published times from https://www.hebcal.com/shabbat. You can also set custom start and end times.
 * Version:           1.0
 * Author:            Orel Krispel
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shabbat-close
*/

include_once(__DIR__ . '/includes/main.php');
include_once(__DIR__ . '/includes/data.php');
include_once(__DIR__ . '/includes/admin.php');
new ShabbatClose();
register_activation_hook(__FILE__, 'shabbat_plugin_activated');

// HERE WE CREATE A TABLE FOR SETTINGS
function shabbat_plugin_activated()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();
    $shabbat_close_settings = "CREATE TABLE `" . SHABBAT_CLOSE_SETTINGS . "` (
        `id` int(11) NOT NULL,
        `geo_name_id` varchar(255) DEFAULT NULL,
        `start_time` varchar(255) DEFAULT NULL,
        `end_time` varchar(255) DEFAULT NULL,
        `redirect_url` text DEFAULT NULL
    ) $charset_collate;
    ";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($shabbat_close_settings);
    empty($wpdb->last_error);
}