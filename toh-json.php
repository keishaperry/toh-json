<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.keishaperry.com/
 * @since             1.0.0
 * @package           Toh_Json
 *
 * @wordpress-plugin
 * Plugin Name:       TOH JSON
 * Plugin URI:        https://www.keishaperry.com/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Keisha Perry
 * Author URI:        https://www.keishaperry.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       toh-json
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TOH_JSON_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-toh-json-activator.php
 */
function activate_toh_json() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-toh-json-activator.php';
	Toh_Json_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-toh-json-deactivator.php
 */
function deactivate_toh_json() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-toh-json-deactivator.php';
	Toh_Json_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_toh_json' );
register_deactivation_hook( __FILE__, 'deactivate_toh_json' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-toh-json.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_toh_json() {

	$plugin = new Toh_Json();
	$plugin->run();
	

}


run_toh_json();

/* $updaters = get_posts( array(
	'numberposts' => -1,
	'post_type'   => 'toh_bonus',
	'post_status' => array('publish','pending'),
	'meta_key' => '_toh_category',
	'meta_value' => 'Hueys',
	'meta_compare' => '=', //
	'orderby' => 'meta_value',
	'order' => 'ASC'
) );
echo count($updaters);
foreach ($updaters as $bonus) {
	$meta = get_post_meta($bonus->ID,'_toh_category',true);
	update_post_meta($bonus->ID,'_toh_category',"AH and UH Helicopters" ,$meta);
	echo "<li>".$meta."</li>";
}
 */
/* global $wpdb;
$bonuses = array();
$table = $wpdb->prefix . "postmeta";
$cats = $wpdb->get_results(  "SELECT DISTINCT `meta_value` FROM $table WHERE `meta_key`= '_toh_category' ORDER BY $table.`meta_value` ASC" ) ;
var_dump($cats); */
/* $result = $wpdb->get_results(  "SELECT DISTINCT `meta_value` FROM $table WHERE `meta_key`= '_toh_state' " ) ;
if (!is_null($result)){
	//var_dump($result);
	foreach ($result as $state){
		echo $state->meta_value;
	}
}	 */

