<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.keishaperry.com/
 * @since      1.0.0
 *
 * @package    Toh_Json
 * @subpackage Toh_Json/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Toh_Json
 * @subpackage Toh_Json/includes
 * @author     Keisha Perry <hire@keishaperry.com>
 */
class Toh_Json {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Toh_Json_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'TOH_JSON_VERSION' ) ) {
			$this->version = TOH_JSON_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'toh-json';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		//date_default_timezone_set('EDT');

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Toh_Json_Loader. Orchestrates the hooks of the plugin.
	 * - Toh_Json_i18n. Defines internationalization functionality.
	 * - Toh_Json_Admin. Defines all hooks for the admin area.
	 * - Toh_Json_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-toh-json-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-toh-json-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-toh-json-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-toh-json-public.php';

		$this->loader = new Toh_Json_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Toh_Json_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Toh_Json_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Toh_Json_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_admin, 'create_bonus_post_type' );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_box_data' );
		$this->loader->add_action( 'save_post', $plugin_admin, 'update_datastore_meta' );
		//$this->loader->add_action( 'admin_menu', $plugin_admin, 'create_settings' );
		//$this->loader->add_action( 'admin_init', $plugin_admin, 'setup_sections' );
		//$this->loader->add_action( 'admin_init', $plugin_admin, 'setup_fields' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_pages' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'kpupdaters' );
		$this->loader->add_action( 'admin_post_trigger_scrape', $plugin_admin, 'trigger_scrape' );
		$this->loader->add_action( 'admin_post_import_kml', $plugin_admin, 'import_kml' );
		$this->loader->add_action( 'admin_post_create_json_record', $plugin_admin, 'create_json_record' );
		$this->loader->add_action( 'admin_post_trigger_scrape_db', $plugin_admin, 'trigger_scrape_db' );
		$this->loader->add_action( 'admin_post_trigger_purge_db', $plugin_admin, 'trigger_purge_db' );
		$this->loader->add_action( 'wp_trash_post', $plugin_admin, 'fake_delete' );



		$this->loader->add_filter( 'manage_edit-toh_bonus_columns', $plugin_admin, 'custom_toh_bonus_cols');
		$this->loader->add_filter( 'manage_toh_bonus_posts_custom_column', $plugin_admin, 'custom_toh_bonus_col_content' );
		$this->loader->add_filter( 'manage_edit-toh_bonus_sortable_columns', $plugin_admin, 'sortable_toh_bonus_col' );
		$this->loader->add_filter( 'pre_get_posts-toh_bonus_sortable_columns', $plugin_admin, 'manage_toh_bonus_pre_get_posts', 1 );
		$this->loader->add_filter( 'pre_get_posts', $plugin_admin, 'custom_search_query', 1 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Toh_Json_Public( $this->get_plugin_name(), $this->get_version() );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'rest_api_init', $plugin_public, 'register_api_hooks' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Toh_Json_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
