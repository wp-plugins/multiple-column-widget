<?php
/*
	Plugin Name: Multiple Column Widget
	Plugin URI: http://www.sanusiyaakub.org
	Description: You will be able to put a list of posts in a single row
	Author: Sanusi Yaakub (sanusi87@gmail.com)
	Version: 0.1
	Author URI: http://visitmeifyoulike.blogspot.com/
*/

global $mcw_db_version;
$mcw_db_version = '1.0';

function mcw_install(){
	global $wpdb;
	global $mcw_db_version;

	$table_name = $wpdb->prefix . 'mcw_widget';
	$table_item_name = $wpdb->prefix . 'mcw_widget_item';
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id int(10) NOT NULL AUTO_INCREMENT,
		name varchar(255) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

	$sql_item = "CREATE TABLE $table_item_name (
		widget_id int(10) NOT NULL,
		post_id int(10) NOT NULL
	) $charset_collate;";
	
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
	dbDelta( $sql_item );

	add_option( 'mcw_db_version', $mcw_db_version );	
}
register_activation_hook( __FILE__, 'mcw_install' );

function mcw_update_db_check() {
	global $mcw_db_version;
	$installed_ver = get_option( "mcw_db_version" );
	if ( $installed_ver != $mcw_db_version ) {
		mcw_install();
	}
}
add_action( 'plugins_loaded', 'mcw_update_db_check' );
?>