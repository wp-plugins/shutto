<?php
/**
 * @package Akismet
 */
/*
Plugin Name: shutto
Plugin URI: 
Description: shuttoは、PCサイトをスマートフォンサイトに変換するサービスです。このプラグインは、shuttoを使って変換したWordPressのサイトにJavaScriptタグを挿入し、スマートフォン対応サイトにすることができます。
Version: 1.0.0
Author: e-Agency Co LTD.
Author URI: 
License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/


define('SHUTTO_VERSION', '1.0.0');
define('SHUTTO_PLUGIN_URL', plugin_dir_url( __FILE__ ));

register_activation_hook(__FILE__, 'activation_func');

if ( is_admin() ){
	require_once dirname( __FILE__ ) . '/admin.php';
}else{
	require_once dirname( __FILE__ ) . '/front.php';
}

function activation_func(){
   global $wpdb;

   $table_name = $wpdb->prefix . "shutto_settings";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
      
   $sql = "CREATE TABLE " . $table_name . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  account_name varchar(255) DEFAULT NULL,
	  UNIQUE KEY id (id)
	);";

      require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
      dbDelta($sql);

   }


}

