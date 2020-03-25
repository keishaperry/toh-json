<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.keishaperry.com/
 * @since      1.0.0
 *
 * @package    Toh_Json
 * @subpackage Toh_Json/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Toh_Json
 * @subpackage Toh_Json/admin
 * @author     Keisha Perry <hire@keishaperry.com>
 */
class Toh_Json_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/toh-json-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( 'uikit', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit.min.js', array( 'jquery' ), '3.0.0-rc.25', false );
		wp_enqueue_script( 'uikit-icons', 'https://cdnjs.cloudflare.com/ajax/libs/uikit/3.0.0-rc.25/js/uikit-icons.min.js', array( 'jquery' ), '3.0.0-rc.25', false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/toh-json-admin.js', array( 'jquery' ), $this->version, false );

	}

	public function create_settings() {
		$page_title = 'TOH Admin';
		$menu_title = 'TOH Admin';
		$capability = 'edit_pages';
		$slug = 'toh';
		$callback = array($this, 'settings_content');
		$icon = 'dashicons-clipboard';
		$position = 2;
		add_menu_page($page_title, $menu_title, $capability, $slug, $callback, $icon, $position);
	}

	public function settings_content() {

		if (isset($_REQUEST["kp-911"]) && $_REQUEST["kp-911"]) {
			$tables = array("toh_bonuses","toh_bonus_json","toh_bonuses_data");
			foreach ($tables as $table) {
				$table_remove = $wpdb->prefix . $table;
				echo $table_remove;				
				$sql = "DROP TABLE IF EXISTS $table_remove;";
				$remove = $wpdb->query($sql);
				var_dump($remove);
			}
		}



		include plugin_dir_path( __FILE__ ) . 'partials/toh-json-admin-display.php';
	}

	public function setup_sections() {
		add_settings_section( 'toh_section', '', array(), 'toh' );
	}

	public function setup_fields() {
		$fields = array(
		);
		foreach( $fields as $field ){
			add_settings_field( $field['id'], $field['label'], array( $this, 'field_callback' ), 'toh', $field['section'], $field );
			register_setting( 'toh', $field['id'] );
		}
	}

	public function field_callback( $field ) {
		$value = get_option( $field['id'] );
		switch ( $field['type'] ) {
			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$field['placeholder'],
					$value
				);
		}
		if( $desc = $field['desc'] ) {
			printf( '<p class="description">%s </p>', $desc );
		}
	}

	public function add_admin_pages(){
		add_submenu_page(
			'edit.php?post_type=toh_bonus',
			__( 'Import Data', 'tohdb' ),
			__( 'Import Data', 'tohdb' ),
			'manage_options',
			'import-toh-data',
			array($this,'import_data_page_callback')
		);
	}
	public function import_data_page_callback(){
		include plugin_dir_path( __FILE__ ) . 'partials/toh-json-admin-import.php';
	}


	public function update_datastore_meta(){
		$opt =  "_tohlastchanged";
		$last_changed = get_option($opt, false );
		if ($last_changed){
			update_option( $opt, time());

		} else {
			add_option( $opt, time());
		}
	}


	public function trigger_scrape(){
		$json = json_decode($this->curl_prod_json());
		$i = 0;
		$scraped_count = 0;
		foreach ($json->bonuses as $bonus){
			$ids = get_posts( array(
				'numberposts' => 1,
				'post_type'   => array('toh_bonus'),
				'post_status'   => array( 'publish','pending','draft','auto-draft','future','private','inherit'),
				'fields'      => 'ids',
				'meta_key' => '_toh_bonusCode',
				'meta_value' => sanitize_text_field($bonus->bonusCode),
				'meta_compare' => '==', 
			) );
			if (is_array($ids) && count($ids) > 0){
				//POST w/ this bonusCode exists
				//todo : update metadata?
			} else {
					$the_post = array(
						'post_title'    => wp_strip_all_tags( $bonus->name ),
						'post_content'  => "",
						'post_status'   => 'pending',
						'post_author'   => 1,
						'post_type'   => 'toh_bonus',
						'meta_input'   => array(
							'_toh_bonusCode' => sanitize_text_field($bonus->bonusCode),
							'_toh_category' => sanitize_text_field($bonus->category),
							'_toh_value' => 1,
							'_toh_address' => sanitize_text_field($bonus->address),
							'_toh_city' => sanitize_text_field($bonus->city),
							'_toh_state' => sanitize_text_field($bonus->state),
							'_toh_GPS' => sanitize_text_field($bonus->GPS),
							'_toh_Access' => sanitize_text_field($bonus->Access),
							'_toh_imageName' => sanitize_text_field($bonus->imageName),
							'_toh_flavor' => sanitize_text_field($bonus->flavor),
							'_toh_madeinamerica' => sanitize_text_field($bonus->madeinamerica),
						),
					);
					// Insert the post into the database
					wp_insert_post( $the_post );
					$scraped_count++; 			
			}
			$i++;

		}
		wp_redirect( admin_url( 'admin.php?page=toh' ) );
		return true;
	}
	public function import_kml(){
		// get import files by extension
		foreach ( glob( plugin_dir_path( __DIR__ ) .'_import/kml/*.kml' ) as $kml ) {
			$xml = simplexml_load_file($kml);
	
			foreach ($xml->Document->Placemark as $k => $v){
			  if ($k !== "Placemark" ) return;
				$x = $v->asXML();
				$datasets = explode("<br><br>",$x);
	
				$datapoints = explode("<br>",$datasets[1]);
				$datapoints = array_filter($datapoints);
				$datapoints = array_values($datapoints);
				$name = explode("-",$v->name);
				$location = explode(",",$datapoints[2]);
				$address = $datapoints[0]."<br>".$datapoints[1];
				$gps = str_ireplace("GPS:","",$datapoints[3]);
				$access = !empty($datapoints[4]) ? str_ireplace("Access:","",$datapoints[4]) : "";
				$description = $datasets[2];
				switch(trim($location[1])){
					case "North Dakota":
					case "South Dakota":
						$region = "Dakotas";
						break;
					case "Delaware":
					case "Maryland":
					case "New Jersey":
					case "Washington DC":
						$region = "Mid-Atlantic";
						break;
					case "Connecticut":
					case "Maine":
					case "Massachusetts":
					case "New Hampshire":
					case "Rhode Island":
					case "Vermont":
						$region = "New England";
						break;
					default:
						$region = trim($location[1]);	
				}
				$bonus_id = trim($name[0]);
				$code = substr($bonus_id,0,2).str_pad(substr($bonus_id,2),3,"0",STR_PAD_LEFT);
				$data = array(
					'post_title'    => trim($name[1]),
					'post_content'  => "",
					'post_status'   => 'publish',
					'post_author'   => 1,
					'post_type'   => 'toh_bonus',
					'meta_input'   => array(
						'_toh_bonusCode' => $code,
						'_toh_category' => "Tour of Honor",
						'_toh_region' => trim($region),
						'_toh_value' => 1,
						'_toh_address' => sanitize_text_field(trim($address)),
						'_toh_city' => sanitize_text_field(trim($location[0])),
						'_toh_state' => !empty($location[1]) ? $this->format_state(trim($location[1]),"abbr") : "",
						'_toh_GPS' => sanitize_text_field(trim($gps)),
						'_toh_Access' => trim($access),
						'_toh_imageName' => "2020".$code.".jpg",
						'_toh_flavor' => sanitize_text_field($description),
					),
				);
				//var_dump($bonus);
				$bonus = (object) $data;

				$ids = get_posts( array(
					'numberposts' => 1,
					'post_type'   => array('toh_bonus'),
					'post_status'   => array( 'publish','pending','draft','auto-draft','future','private','inherit'),
					'fields'      => 'ids',
					'meta_key' => '_toh_bonusCode',
					'meta_value' => trim($name[0]),
					'meta_compare' => '==', 
				) );
				if (is_array($ids) && count($ids) > 0){
					//POST w/ this bonusCode exists
					//todo : update metadata?
				echo "Bonus ".trim($name[0])." already exists. <br>";
				} else {
						// Insert the post into the database
						wp_insert_post( $bonus );
				}
			}
		}
		wp_redirect(admin_url("edit.php?post_type=toh_bonus"));
	}
	public function trigger_scrape_db(){
		$db_tablename = $_REQUEST["db_tablename"];

		global $wpdb;
		$table = $db_tablename;
		$result = $wpdb->get_results(  "SELECT * FROM $table" ) ;
		$data = $this->map_db_import($db_tablename,$result);
		$import = $this->run_db_import_to_post($data);	
		wp_redirect( admin_url( 'edit.php?post_type=toh_bonus&page=import-toh-data' ) );
		return true;
	}
	public function trigger_purge_db(){
		$updaters = get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'toh_bonus',
			'post_status' => array('publish','pending'),
			'order' => 'ASC',
			'orderby' => 'meta_field',
		/* 	'meta_key' => '_toh_category',
			'meta_value' => 'National Parks' */
			'meta_query' => array( 
				'relation' => 'OR', 
				 array(
				   'key' => '_toh_category', 
				   'value' => 'National Parks', 
				   'type' => 'CHAR', 
				   'compare' => '=',
				 ),
				 array(
				   'key' => '_toh_category', 
				   'value' => 'Hueys', 
				   'type' => 'CHAR', 
				   'compare' => '=',
				 ),
				 array(
				   'key' => '_toh_category', 
				   'value' => 'Gold Star Family', 
				   'type' => 'CHAR', 
				   'compare' => '=',
				 ),
				 array(
				   'key' => '_toh_category', 
				   'value' => 'War Dogs', 
				   'type' => 'CHAR', 
				   'compare' => '=',
				 ),
				 array(
				   'key' => '_toh_category', 
				   'value' => 'Doughboys', 
				   'type' => 'CHAR', 
				   'compare' => '=',
				 ),

			  ),
		) );
		//echo count($updaters);exit;
		foreach ($updaters as $bonus) {
			$this->change_post_status($bonus->ID,'trash');
			//$meta = get_post_meta($bonus->ID,'_toh_bonusCode', true);
			//$code = str_ireplace('MTr','',$meta);
			//$new_code = "MTr".str_pad($code,3,"0",STR_PAD_LEFT);
			//echo $new_code."<br>";
			//update_post_meta($bonus->ID,'_toh_bonusCode',$new_code);
			//wp_update_post($post);
		
		}
		wp_redirect( admin_url( 'admin.php?page=toh' ) );
		return true;
	}

	public function run_db_import_to_post($data){
		foreach ($data as $item){
			$bonus = (object) $item;

			$ids = get_posts( array(
				'numberposts' => 1,
				'post_type'   => array('toh_bonus'),
				'post_status'   => array( 'publish','pending','draft','auto-draft','future','private','inherit'),
				'fields'      => 'ids',
				'meta_key' => '_toh_bonusCode',
				'meta_value' => sanitize_text_field($bonus->meta_input['_toh_bonusCode']),
				'meta_compare' => '==', 
			) );
			if (is_array($ids) && count($ids) > 0){
				//POST w/ this bonusCode exists
				//todo : update metadata?
				echo "bonus already exists!"; exit;
			} else {
					$the_post = $item;
					// Insert the post into the database
					wp_insert_post( $the_post );
			}
			$i++;

		}
	}
	public function map_db_import($db_tablename,$result){
		$data = array();
		switch($db_tablename){
			case 'dogs':
				foreach ($result as $bonus){
					$bonus = array(
						'post_title'    => wp_strip_all_tags( $bonus->dog_name ),
						'post_content'  => "",
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'   => 'toh_bonus',
						'meta_input'   => array(
							'_toh_bonusCode' => "K9-".str_pad($bonus->dog_id,3,"0",STR_PAD_LEFT),
							'_toh_category' => "War Dogs",
							'_toh_value' => 1,
							'_toh_address' => sanitize_text_field($bonus->dog_addr),
							'_toh_city' => sanitize_text_field($bonus->dog_city),
							'_toh_state' => sanitize_text_field($bonus->dog_state),
							'_toh_region' => "unneeded",
							'_toh_GPS' => sanitize_text_field($bonus->dog_gps),
							'_toh_Access' => '',
							'_toh_imageName' => "2020dogs".str_pad($bonus->dog_id,3,"0",STR_PAD_LEFT).".jpg",
							'_toh_flavor' => sanitize_text_field($bonus->dog_desc)."\n".sanitize_text_field($bonus->dog_link),
							'_toh_madeinamerica' => '',
						),
					);
					array_push($data,$bonus);
				}
				break;
			case 'doughboys':
				foreach ($result as $bonus){
					$bonus = array(
						'post_title'    => wp_strip_all_tags( $bonus->doughboy_name ),
						'post_content'  => "",
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'   => 'toh_bonus',
						'meta_input'   => array(
							'_toh_bonusCode' => "DB".str_pad($bonus->doughboy_id,3,"0",STR_PAD_LEFT),
							'_toh_category' => "Doughboys",
							'_toh_value' => 1,
							'_toh_address' => sanitize_text_field($bonus->doughboy_location),
							'_toh_city' => sanitize_text_field($bonus->doughboy_city),
							'_toh_state' => sanitize_text_field($bonus->doughboy_state),
							'_toh_region' => "unneeded",
							'_toh_GPS' => sanitize_text_field($bonus->doughboy_gps),
							'_toh_Access' => '',
							'_toh_imageName' => "2020doughboys".str_pad($bonus->doughboy_id,3,"0",STR_PAD_LEFT).".jpg",
							'_toh_flavor' => sanitize_text_field($bonus->doughboy_desc),
							'_toh_madeinamerica' => '',
						),
					);
					array_push($data,$bonus);
				}
				break;
			case 'goldstars':
				foreach ($result as $bonus){
					$bonus = array(
						'post_title'    => wp_strip_all_tags( $bonus->gs_desc ),
						'post_content'  => "",
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'   => 'toh_bonus',
						'meta_input'   => array(
							'_toh_bonusCode' => "GS".str_pad($bonus->gs_id,3,"0",STR_PAD_LEFT),
							'_toh_category' => "Gold Star Family",
							'_toh_value' => 1,
							'_toh_address' => sanitize_text_field($bonus->gs_addr),
							'_toh_city' => sanitize_text_field($bonus->gs_city),
							'_toh_state' => sanitize_text_field($bonus->gs_state),
							'_toh_region' => "unneeded",
							'_toh_GPS' => sanitize_text_field($bonus->gs_gps),
							'_toh_Access' => '',
							'_toh_imageName' => "2020goldstars".str_pad($bonus->gs_id,3,"0",STR_PAD_LEFT).".jpg",
							'_toh_flavor' => sanitize_text_field($bonus->gs_link),
							'_toh_madeinamerica' => '',
						),
					);
					array_push($data,$bonus);
				}
				break;
			case 'hueys':
				foreach ($result as $bonus){
					$bonus = array(
						'post_title'    => wp_strip_all_tags( $bonus->huey_name ),
						'post_content'  => "",
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'   => 'toh_bonus',
						'meta_input'   => array(
							'_toh_bonusCode' => "H".str_pad($bonus->huey_id,3,"0",STR_PAD_LEFT),
							'_toh_category' => "Hueys",
							'_toh_value' => 1,
							'_toh_address' => sanitize_text_field($bonus->huey_addr),
							'_toh_city' => sanitize_text_field($bonus->huey_city),
							'_toh_state' => sanitize_text_field($bonus->huey_state),
							'_toh_region' => "unneeded",
							'_toh_GPS' => sanitize_text_field($bonus->huey_gps),
							'_toh_Access' => '',
							'_toh_imageName' => "2020hueys".str_pad($bonus->huey_id,3,"0",STR_PAD_LEFT).".jpg",
							'_toh_flavor' => sanitize_text_field($bonus->huey_desc)."\n".sanitize_text_field($bonus->huey_link),
							'_toh_madeinamerica' => '',
						),
					);
					array_push($data,$bonus);
				}
				break;
			case 'parks':
				foreach ($result as $bonus){
					$bonus = array(
						'post_title'    => wp_strip_all_tags( $bonus->park_name ),
						'post_content'  => "",
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'   => 'toh_bonus',
						'meta_input'   => array(
							'_toh_bonusCode' => "NP".str_pad($bonus->park_id,3,"0",STR_PAD_LEFT),
							'_toh_category' => "National Parks",
							'_toh_value' => 1,
							'_toh_address' => '',
							'_toh_city' => sanitize_text_field($bonus->park_city),
							'_toh_state' => sanitize_text_field($bonus->park_state),
							'_toh_region' => "unneeded",
							'_toh_GPS' => sanitize_text_field($bonus->park_gps),
							'_toh_Access' => '',
							'_toh_imageName' => "2020parks".str_pad($bonus->park_id,3,"0",STR_PAD_LEFT).".jpg",
							'_toh_flavor' => sanitize_text_field($bonus->park_type)."\n".sanitize_text_field($bonus->park_link),
							'_toh_madeinamerica' => '',
						),
					);
					array_push($data,$bonus);
				}
				break;
			case 'madonnas':
				foreach ($result as $bonus){
					$code = "MTr".str_pad(str_replace("MTr","",$bonus->madonna_abbr),3,"0",STR_PAD_LEFT);
					$bonus = array(
						'post_title'    => wp_strip_all_tags( "Madonna of the Trail, ". $this->format_state($bonus->madonna_state, "abbr") ),
						'post_content'  => "",
						'post_status'   => 'publish',
						'post_author'   => 1,
						'post_type'   => 'toh_bonus',
						'meta_input'   => array(
							'_toh_bonusCode' => $code,
							'_toh_category' => "Madonna of the Trail",
							'_toh_value' => 1,
							'_toh_address' => $bonus->madonna_location,
							'_toh_city' => sanitize_text_field($bonus->madonna_city),
							'_toh_state' => sanitize_text_field($bonus->madonna_state),
							'_toh_region' => "unneeded",
							'_toh_GPS' => sanitize_text_field($bonus->madonna_gps),
							'_toh_imageName' => "2020parks".$code.".jpg",
						),
					);
					array_push($data,$bonus);
				}
				break;
			default:
				$data = false;
		}
		return $data;
	}

	public function curl_prod_json(){
		$target_url = "http://localhost/toh/bonusData.json";
		$curl = curl_init( $target_url );
		curl_setopt($curl, CURLOPT_VERBOSE, true);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		$response = curl_exec( $curl );
		curl_close( $curl );
		if (is_null($response)) {
			echo '<div class="uk-text-danger uk-alert-danger uk-padding-small"><b>Error connecting:</b><p>cUrl error ('.curl_errno($ch).'): '.htmlspecialchars(curl_error($ch)).'</p></div>';
		}

		return $response;
	}

	public function create_bonus_post_type() {
		register_post_type( 'toh_bonus',
		  array(
			'labels' => array(
			  'name' => __( 'Bonuses' ),
			  'singular_name' => __( 'Bonus' )
			),
			'menu_position' => 3,
			'supports' => array('title'),
			'taxonomies' => array(),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => false,
			'show_in_nav_menus' => false,
			'can_export' => true,
			'has_archive' => false,
			'hierarchical' => false,
			'exclude_from_search' => false,
			'show_in_rest' => true,
			'publicly_queryable' => true,
			'capability_type' => 'post',
		  )
		);
	}
	
	function add_meta_boxes( $post ){
		add_meta_box( 'toh_bonus_meta', __( 'Bonus Data', 'toh' ), array($this,'build_meta_box'), 'toh_bonus', 'advanced', 'high' );
	}

	function build_meta_box( $post ){
		// make sure the form request comes from WordPress
		wp_nonce_field( basename( __FILE__ ), 'toh_bonus_meta_nonce' );
		$meta = get_post_meta($post->ID);
		ob_start();
		include( plugin_dir_path (__FILE__ ) . 'partials/toh-json-bonus-metabox.php');
		echo ob_get_clean();
	}

	function save_meta_box_data( $post_id ){
		// verify meta box nonce
		if ( !isset( $_POST['toh_bonus_meta_nonce'] ) || !wp_verify_nonce( $_POST['toh_bonus_meta_nonce'], basename( __FILE__ ) ) ){
			return;
		}
		// return if autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			return;
		}
		// Check the user's permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ){
			return;
		}
		$meta_fields = array(
			'bonusCode' => '_toh_bonusCode',
			'category' => '_toh_category',
			'region' => '_toh_region',
			'value' => '_toh_value',
			'address' => '_toh_address',
			'city' => '_toh_city',
			'state' => '_toh_state',
			'GPS' => '_toh_GPS',
			'Access' => '_toh_Access',
			'imageName' => '_toh_imageName',
			'flavor' => '_toh_flavor',
			'madeinamerica' => '_toh_madeinamerica',
		);
		// store custom fields values
		foreach ($meta_fields as $post => $field) {
			update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$post] ) );
		}

	}
	public function get_next_version(){
		$opt =  "_tohlastchanged";
		$last_changed = get_option($opt, false );
		if ($last_changed){
			add_option( $opt, time());
			return time();
		} else {
			return $last_changed;
		}
	}

	public function get_current_version(){
		global $wpdb;
		$table = $wpdb->prefix . "toh_json_database";
		$result = $wpdb->get_row(  "SELECT `version` FROM $table ORDER BY `created_at` DESC LIMIT 1" ) ;
		if (!is_null($result)){
			return $result->version;		
		} else {
			$live_json = json_decode($this->curl_prod_json());
			return $live_json->meta->version;
		}
	}

	public function create_json_record(){
		global $wpdb;
		$next_version =$this->get_next_version();

		$now = time();
		//1585717260 = 4-1-2020 1am EDT
		$then =  "1585717260";
		/* 
		if ($now >= $then){
			echo "we're open for biz, boys";
		} else {
			echo "not quite there yet";
		} */
		$bonuses = array();
		$table = $wpdb->prefix . "postmeta";
		//loop each state alphabetically -- grab all meta values from db query?
		$result = $wpdb->get_results(  "SELECT DISTINCT `meta_value` FROM $table WHERE `meta_key`= '_toh_state' ORDER BY $table.`meta_value` ASC" ) ;
		$cats = $wpdb->get_results(  "SELECT DISTINCT `meta_value` FROM $table WHERE `meta_key`= '_toh_category' ORDER BY $table.`meta_value` ASC" ) ;
		if (!is_null($result)){
			foreach ($result as $state){
				//grab TOH cat for state, add
				//echo "<h1>".$state->meta_value."</h1>";
				//echo "<li>TOH</li>";
				if ($now >= $then){
					foreach ( $this->get_certain_bonues("Tour of Honor",$state->meta_value) as $bonus){
						array_push($bonuses, $bonus);
					}
				}
				
				//grab foreach other cats alphabetically, add
				foreach ($cats as $cat){
					if ($cat->meta_value != "Tour of Honor") {
						foreach ( $this->get_certain_bonues($cat->meta_value,$state->meta_value) as $bonus){
							array_push($bonuses, $bonus);
						}
						//echo "<li>".$cat->meta_value."</li>";
					}

				}
				
			}
		}	

		$insert =  $bonuses;
		$this->store_json_record($insert);
		return wp_redirect( admin_url( 'admin.php?page=toh' ) );

	}

	public function get_certain_bonues($category,$state){
		$bonuses = array();
		$bonus_posts =  get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'toh_bonus',
			'post_status' => array('publish','pending'),
			//'meta_key' => '_toh_category',
			//'meta_value' => $category,
			'meta_query' => array( // (array) - Custom field parameters (available with Version 3.1).
				'relation' => 'AND', // (string) - Possible values are 'AND', 'OR'. The logical relationship between each inner meta_query array when there is more than one. Do not use with a single inner meta_query array.
				 array(
					 'key' => '_toh_category', // (string) - Custom field key.
					 'value' => $category, // (string/array) - Custom field value (Note: Array support is limited to a compare value of 'IN', 'NOT IN', 'BETWEEN', or 'NOT BETWEEN') Using WP < 3.9? Check out this page for details: http://codex.wordpress.org/Class_Reference/WP_Query#Custom_Field_Parameters
					 'type' => 'CHAR', // (string) - Custom field type. Possible values are 'NUMERIC', 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL', 'SIGNED', 'TIME', 'UNSIGNED'. Default value is 'CHAR'. The 'type' DATE works with the 'compare' value BETWEEN only if the date is stored at the format YYYYMMDD and tested with this format.
					 'compare' => '=', // (string) - Operator to test. Possible values are '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'EXISTS' (only in WP >= 3.5), and 'NOT EXISTS' (also only in WP >= 3.5). Default value is '='.
				 ),
				 array(
					 'key' => '_toh_state',
					 'value' => $state,
					 'compare' => '=',
				 )
			),
		) );
		foreach ($bonus_posts as $bonus) {
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
			);
			array_push($bonuses,$bonus);
		} 
		return $bonuses;
	}

	public function store_json_record($data){
		global $wpdb;
		date_default_timezone_set('America/Los_Angeles');

		$user = get_current_user_id();
		$next_version = $this->get_next_version();


		$table = $wpdb->prefix . "toh_json_database";
		$wpdb->insert( 
			$table, 
			array( 
				'created_at' => current_time( 'mysql' ), 
				'created_by' => $user, 
				'json_file' =>  json_encode($data,JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_LINE_TERMINATORS| JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT ), 
				'version' =>  $next_version, 
			) 
		);
	}

	public function get_bonus_json_records(){
		global $wpdb;
		$table = $wpdb->prefix . "toh_json_database";
			$result = $wpdb->get_results(  "SELECT * FROM $table ORDER BY `created_at` DESC LIMIT 25" ) ;
			return (array)$result;		
	}



	function custom_toh_bonus_cols($columns) {
		$columns['bonusCode'] = 'Bonus Code';
		$columns['toh_city'] = 'City';
		$columns['toh_region'] = 'Region';
		return $columns;
	}
	function custom_toh_bonus_col_content( $column_name ) {
		if ( 'bonusCode' === $column_name ){
			global $post;
			
			$meta = get_post_meta($post->ID, '_toh_bonusCode', true);
			echo $meta;
		} else if ('toh_city' === $column_name){
			global $post;
			
			$meta = get_post_meta($post->ID, '_toh_city', true);
			echo $meta;
		} else if ('toh_region' === $column_name){
			global $post;
			
			$meta = get_post_meta($post->ID, '_toh_region', true);
			echo $meta;
		}
		
	}
	function sortable_toh_bonus_col( $columns ) {
		//$columns['bonusCode'] = 'toh_bonusCode'; 
		$columns['toh_region'] = 'toh_region'; 
		return $columns;
	}
	function manage_toh_bonus_pre_get_posts( $query ) {

	   if ( $query->is_main_query() && ( $orderby = $query->get( 'orderby' ) ) ) {
		  switch( $orderby ) {
			
			 case 'toh_region':
				$query->set('meta_key', 'toh_region');
				$query->set('orderby', array(
					'meta_value' => 'ASC',
					'post_date'  => 'ASC',
				));

				break;
		  }
	   }
	}


	
function change_post_status($post_id,$status){
	$current_post = get_post( $post_id, 'ARRAY_A' );
	$current_post['post_status'] = $status;
	wp_update_post($current_post);
}

function kpupdaters(){

	if (isset($_REQUEST["remove-city-astchar"])){
		$updaters = get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'toh_bonus',
			'post_status' => array('publish','pending','draft'),
		) );
		foreach ($updaters as $bonus) {
			$meta = get_post_meta($bonus->ID,'_toh_city', true);
			$new = trim($meta,"*");
			update_post_meta($bonus->ID,'_toh_city',$new);
			//wp_update_post($post);
		
		}
	}

	/* $updaters = get_posts( array(
		'numberposts' => -1,
		'post_type'   => 'toh_bonus',
		'post_status' => array('publish','pending'),
		'order' => 'ASC',
		'orderby' => 'meta_field',
		'meta_key' => '_toh_category',
		'meta_value' => 'Madonna Trail'
	) ); */
	//echo count($updaters);exit;
	/* foreach ($updaters as $bonus) {
		//$this->change_post_status($bonus->ID,'draft');
		//$meta = get_post_meta($bonus->ID,'_toh_bonusCode', true);
		//$code = str_ireplace('MTr','',$meta);
		//$new_code = "MTr".str_pad($code,3,"0",STR_PAD_LEFT);
		//echo $new_code."<br>";
		//update_post_meta($bonus->ID,'_toh_bonusCode',$new_code);
		//wp_update_post($post);
	
	} */
}
function format_state( $input, $format = '' ) {
	if( ! $input || empty( $input ) )
		return;

		$states = array(
			'Alabama'=>'AL',
			'Alaska'=>'AK',
			'Arizona'=>'AZ',
			'Arkansas'=>'AR',
			'California'=>'CA',
			'Colorado'=>'CO',
			'Connecticut'=>'CT',
			'Delaware'=>'DE',
			'Florida'=>'FL',
			'Georgia'=>'GA',
			'Hawaii'=>'HI',
			'Idaho'=>'ID',
			'Illinois'=>'IL',
			'Indiana'=>'IN',
			'Iowa'=>'IA',
			'Kansas'=>'KS',
			'Kentucky'=>'KY',
			'Louisiana'=>'LA',
			'Maine'=>'ME',
			'Maryland'=>'MD',
			'Massachusetts'=>'MA',
			'Michigan'=>'MI',
			'Minnesota'=>'MN',
			'Mississippi'=>'MS',
			'Missouri'=>'MO',
			'Montana'=>'MT',
			'Nebraska'=>'NE',
			'Nevada'=>'NV',
			'New Hampshire'=>'NH',
			'New Jersey'=>'NJ',
			'New Mexico'=>'NM',
			'New York'=>'NY',
			'North Carolina'=>'NC',
			'North Dakota'=>'ND',
			'Ohio'=>'OH',
			'Oklahoma'=>'OK',
			'Oregon'=>'OR',
			'Pennsylvania'=>'PA',
			'Rhode Island'=>'RI',
			'South Carolina'=>'SC',
			'South Dakota'=>'SD',
			'Tennessee'=>'TN',
			'Texas'=>'TX',
			'Utah'=>'UT',
			'Vermont'=>'VT',
			'Virginia'=>'VA',
			'Washington'=>'WA',
			'West Virginia'=>'WV',
			'Wisconsin'=>'WI',
			'Wyoming'=>'WY'
			);

	if (!empty($states[$input])){
		return $states[$input];
	}
	return $input;
}




}
