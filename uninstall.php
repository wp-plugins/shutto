<?php
/*
Last modified: 2012/10/10
License: GPL2
Author URI: http://www.e-agency.co.jp/
*/
if ( !defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN') ) { exit(); }
global $wpdb;
$table_name = $wpdb->prefix . "shutto_settings";
$sql = "DROP TABLE IF EXISTS $table_name;";
$e = $wpdb->query($sql);
?>