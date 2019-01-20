<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.keishaperry.com/
 * @since      1.0.0
 *
 * @package    Toh_Json
 * @subpackage Toh_Json/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Toh_Json
 * @subpackage Toh_Json/public
 * @author     Keisha Perry <hire@keishaperry.com>
 */
class Toh_Json_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Toh_Json_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Toh_Json_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/toh-json-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Toh_Json_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Toh_Json_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/toh-json-public.js', array( 'jquery' ), $this->version, false );

	}

	public function get_bonus_json_version($version){
		global $wpdb;
		$table = $wpdb->prefix . "toh_bonuses";
		$result = $wpdb->get_row(  "SELECT * FROM $table WHERE `version` = '$version' LIMIT 1" ) ;
		return (array)$result;
		
	}



	public function register_api_hooks(){
        // Add y/v1/get-all-post-ids route
        register_rest_route( 'toh/v1', '/bonus-data/', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_bonus_data'],
        ) );

	}
	public function get_bonus_data($data) {
		$filter["version"] = $data->get_param("v");
		$data = $this->get_bonus_json_version($filter["version"]);
		$file = json_decode($data["json_file"]);
		return $file;
	}


}
