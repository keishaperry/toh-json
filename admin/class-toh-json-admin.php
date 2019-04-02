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
	public function trigger_scrape_db(){
		$db_tablename = $_REQUEST["db_tablename"];

		global $wpdb;
		$table = $db_tablename;
			$result = $wpdb->get_results(  "SELECT * FROM $table" ) ;
			$data = $this->map_db_import($db_tablename,$result);
			$import = $this->run_db_import_to_post($data);	
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
						'post_status'   => 'pending',
						'post_author'   => 1,
						'post_type'   => 'toh_bonus',
						'meta_input'   => array(
							'_toh_bonusCode' => "DOG".sanitize_text_field($bonus->dog_id),
							'_toh_category' => "War Dogs",
							'_toh_value' => 1,
							'_toh_address' => sanitize_text_field($bonus->dog_addr),
							'_toh_city' => sanitize_text_field($bonus->dog_city),
							'_toh_state' => sanitize_text_field($bonus->dog_state),
							'_toh_GPS' => sanitize_text_field($bonus->dog_gps),
							'_toh_Access' => '',
							'_toh_imageName' => "2019dogs".sanitize_text_field($bonus->dog_id).".jpg",
							'_toh_flavor' => sanitize_text_field($bonus->dog_desc)."\n".sanitize_text_field($bonus->dog_link),
							'_toh_madeinamerica' => '',
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
		add_meta_box( 'toh_bonus_meta', __( 'Bonus Data', 'toh' ), array($this,'build_meta_box'), $metabox_posts, 'advanced', 'high' );
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
		$current_version = $this->get_current_version();
		$versions = explode(".",$current_version);
		$versions[2] = $versions[2] + 1;
		return implode(".",$versions);
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
		$next_version =$this->get_next_version();
		$bonuses = array();
		$bonus_posts =  get_posts( array(
			'numberposts' => -1,
			'post_type'   => 'toh_bonus',
			'post_status' => array('publish'),
		) );
		foreach ($bonus_posts as $bonus) {
			$meta = get_post_meta($bonus->ID);
			$bonus = array(
				"bonusCode" => $meta["_toh_bonusCode"][0],
				"category" => $meta["_toh_category"][0],
				"name" => $bonus->post_title,
				"value" => $meta["_toh_value"][0],
				"address" => $meta["_toh_address"][0],
				"city" => $meta["_toh_city"][0],
				"state" => $meta["_toh_state"][0],
				"GPS" => $meta["_toh_GPS"][0],
				"Access" => $meta["_toh_Access"][0],
				"flavor" => $meta["_toh_flavor"][0],
				"madeinamerica" => $meta["_toh_madeinamerica"][0],
				"imageName"=> $meta["_toh_imageName"][0]
			);
			array_push($bonuses,$bonus);
		}
		$insert =  array(
			"meta" => array(
				"fileName" => "Tour of Honor Bonus Listing",
				"version" => $next_version,
			),
			"bonuses" => $bonuses,
		);
		$this->store_json_record($insert);
		return wp_redirect( admin_url( 'admin.php?page=toh' ) );

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






}
