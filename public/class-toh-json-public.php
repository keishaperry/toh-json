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
		$table = $wpdb->prefix . "toh_json_database";
		if ($version){
			$result = $wpdb->get_row(  "SELECT * FROM $table WHERE `version` = '$version' LIMIT 1" ) ;
		} else {
			//$result = $wpdb->get_row(  "SELECT * FROM $table ORDER BY ID DESC LIMIT 1" ) ;
		}
		return (array)$result;
		
	}



	public function register_api_hooks(){
        // Add y/v1/get-all-post-ids route
        register_rest_route( 'toh/v1', '/bonus-data/', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_bonus_data'],
        ) );
        register_rest_route( 'toh/v1', '/updates/', array(
            'methods' => 'GET',
            'callback' => [$this, 'get_updated_data'],
        ) );

	}
	public function get_bonus_data($data) {
		$opt =  "_tohlastchanged";
		$last_changed = get_option($opt, false );
		$filter["version"] = $data->get_param("v");
		if (isset($filter["version"])){
			$data = $this->get_bonus_json_version($filter["version"]);
		} else {
			$now = time();
			$then =  "1585713660";
			$bonuses = [];
			$now = time();
			$args = array(
				'post_type' => "toh_bonus",
				'posts_per_page' => -1,
			);
			if ($now < $then){
				$args['meta_key'] = '_toh_category';
				$args['meta_value'] = 'Tour of Honor';
				$args['meta_compare'] = '!=';
			}
			$changes = get_posts($args);
			foreach ($changes as $bonus) {
				$meta = get_post_meta($bonus->ID);
				$bonus = array(
					"bonusCode" => $meta["_toh_bonusCode"][0],
					"bonusCategory" => $meta["_toh_category"][0],
					"bonusName" => $bonus->post_title,
					"address" => $meta["_toh_address"][0],
					"city" => $meta["_toh_city"][0],
					"state" => $meta["_toh_state"][0],
					"region" => $meta["_toh_region"][0],
					"GPS" => $meta["_toh_GPS"][0],
					"sampleImage"=> !is_null($meta["_toh_imageName"][0]) ? $meta["_toh_imageName"][0] : '',
					"sampleImageURL"=> !is_null($meta["_toh_imageURL"][0]) ? $meta["_toh_imageURL"][0] : '',
				);
				array_push($bonuses,$bonus);
			} 
			return $bonuses;

		}

		$file = json_decode($data["json_file"]);
		return $file;
	}
	
	public function get_updated_data(){
		$bonuses = [];
		$now = time();
		$then =  "1585713660";
		$args = array(
			'post_type' => "toh_bonus",
			'date_query' => array(
				array(
					'column' => 'post_modified',
					'after'  =>  date( 'c' ,$then),
				),
			),
			'posts_per_page' => -1,
		);
		$changes = get_posts($args);
		foreach ($changes as $bonus) {
			$meta = get_post_meta($bonus->ID);
			$bonus = array(
				"bonusCode" => $meta["_toh_bonusCode"][0],
				"bonusCategory" => $meta["_toh_category"][0],
				"bonusName" => $bonus->post_title,
				"address" => $meta["_toh_address"][0],
				"city" => $meta["_toh_city"][0],
				"state" => $meta["_toh_state"][0],
				"region" => $meta["_toh_region"][0],
				"GPS" => $meta["_toh_GPS"][0],
				"sampleImage"=> !is_null($meta["_toh_imageName"][0]) ? $meta["_toh_imageName"][0] : '',
				"sampleImageURL"=> !is_null($meta["_toh_imageURL"][0]) ? $meta["_toh_imageURL"][0] : '',
			);
			array_push($bonuses,$bonus);
		} 
		return $bonuses;
	}


}
