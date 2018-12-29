<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.keishaperry.com/
 * @since      1.0.0
 *
 * @package    Toh_Json
 * @subpackage Toh_Json/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Toh_Json
 * @subpackage Toh_Json/includes
 * @author     Keisha Perry <hire@keishaperry.com>
 */
class Toh_Json_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$table = $wpdb->prefix . "toh_bonuses";
		if($wpdb->get_var( "show tables like '$table'" ) != $table){
			$sql_create_table = "CREATE TABLE $table (
			 `id` int(11) NOT NULL auto_increment,
			 `created_at` timestamp NOT NULL,
			 `published_at` timestamp,
			 `created_by`  VARCHAR(255) NOT NULL,
			 `published_by` int(11),
			 `bonus_info` longtext NOT NULL, 
			 `state` int(2) NOT NULL, 
			 PRIMARY KEY (`id`), 
			 INDEX `TIMESTAMP` (`created_at`)
		   ) $charset_collate; ";
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql_create_table );
		} else {
			//table already exists [todo?]
		}
	}



}
