<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://randovelo.touteslatitudes.fr
 * @since      1.0.0
 *
 * @package    Amecycl
 * @subpackage Amecycl/includes
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
 * @package    Amecycl
 * @subpackage Amecycl/includes
 * @author     toutesLatitudes <contact@randovelo.touteslatitudes.fr>
 */
class Amecycl {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Amecycl_Loader    $loader    Maintains and registers all hooks for the plugin.
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
	 * Store plugin main class to allow public access.
	 *
	 * @since    1.0.0
	 * @var object      The main class.
	 */
	public $main;
		
	/**
	 * The model that's responsible for maintaining and registering all data
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Amecycl_Model    $model    Maintains and registers all data for the plugin.
	 */
	public $model;

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
		
		if ( defined( 'AMECYCL_VERSION' ) ) {
			$this->version = AMECYCL_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'amecycl';
		
		$this->main = $this;

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		
		$this->model = new Amecycl_Model();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Amecycl_Loader. Orchestrates the hooks of the plugin.
	 * - Amecycl_i18n. Defines internationalization functionality.
	 * - Amecycl_Admin. Defines all hooks for the admin area.
	 * - Amecycl_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * La classe responsable du modele de données
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amecycl-model.php';

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amecycl-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-amecycl-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-amecycl-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-amecycl-public.php';

		$this->loader = new Amecycl_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Amecycl_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Amecycl_i18n();

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

		$plugin_admin = new Amecycl_Admin( $this->get_plugin_name(), $this->get_version(), $this->main  );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// menu d'administration
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_menu', 0 );

		// requete ajax suppression d'une region depuis l'admin
		$this->loader->add_action( 'wp_ajax_delete-region', $plugin_admin, 'delete_region', 0 );

		// requete ajax suppression d'une configuration depuis l'admin
		$this->loader->add_action( 'wp_ajax_delete-setting', $plugin_admin, 'delete_setting', 0 );

		// requete ajax pour l'upload d'un fichier region
		$this->loader->add_action( 'wp_ajax_upload-region-file', $plugin_admin, 'upload_region_file', 0 );

	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Amecycl_Public( $this->get_plugin_name(), $this->get_version(), $this->main );

		// fichier css et js
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// requete ajax pour obtenir les amenagements cyclables
		$this->loader->add_action( 'wp_ajax_get-ames', $plugin_public, 'get_ames', 0 );
		$this->loader->add_action( 'wp_ajax_nopriv_get-ames', $plugin_public, 'get_ames', 0 );

		// enregistrement des shortcodes amecycls
		$this->loader->add_shortcode( "amecycl", $plugin_public, "amecycl_shortcode", $priority = 10, $accepted_args = 3 );
		$this->loader->add_shortcode( "amecycl-stats", $plugin_public, "amecycl_stats_shortcode", $priority = 10, $accepted_args = 1 );

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
	 * @return    Amecycl_Loader    Orchestrates the hooks of the plugin.
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
