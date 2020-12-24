<?php
/**
 * Plugin Name:     Map Explorer
 * Plugin URI:      https://XiXiCity.org
 * Description:     This plugin provides an Open Source Map (OSM) with a Javascript interface to load and manipulate markers
 * Author:          Darcy Christ <darcy@aporia.info>, Stan Diers <hello@standiers.com>
 * Author URI:      https://aporia.info
 * Text Domain:     map-explorer
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Map_Explorer
 */


defined( 'ABSPATH' ) || exit;


add_action( 'plugins_loaded', 'map_explorer' );


function map_explorer() {	

	// if ( ! class_exists( 'WooCommerce' ) ) {
	// 	add_action( 'admin_notices', 'woocommerce_stripe_missing_wc_notice' );
	// 	return;
	// }

	final class Map_Explorer {

		/**
		 * Plugin Version
		 *
		 * @since 1.0.0
		 *
		 * @var string The plugin version.
		 */
		const VERSION = '1.0.0';

		/**
		 * Instance
		 *
		 * @since 1.0.0
		 *
		 * @access private
		 * @static
		 *
		 * @var Elementor_Test_Extension The single instance of the class.
		 */
		private static $_instance = null;

		/**
		 * internationalization textdomain
		 */
		public static $textdomain = 'map-explorer';


		/**
		 * Instance
		 *
		 * Ensures only one instance of the class is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 * @static
		 *
		 * @return Elementor_Test_Extension An instance of the class.
		 */
		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;

		}

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 *
		 * @access public
		 */
		public function __construct() {
			add_action( 'plugins_loaded', array( $this, 'load_classes' ), 9 );

			$this->init();
		}


		/**
		 * Instantiate classes when woocommerce is activated
		 */
		public function load_classes() {

			// all systems ready - GO!
			//$this->includes();
		}

		/**
		 *
		 */
		public function init() {

			// load scripts
			add_action( 'init', [ $this, 'init_scripts' ] );
			add_action( 'init', [ $this, 'init_styles' ] );

		}

		/**
		 * init_scripts
		 */
		public function init_scripts() {
			wp_enqueue_script( 
				'leaflet', 
				plugins_url( '/assets/js/leaflet/leaflet.js', __FILE__ ), 
				false, 
				false, 
				true 
			);
			wp_enqueue_script( 
				'map-explorer', 
				plugins_url( '/assets/js/map-explorer.js', __FILE__ ), 
				[
					'jquery',
					'leaflet'
				], 
				false, 
				true 
			);

	        // wp_localize_script('cc-extensions', 'pavilions', array(
	        //     'ajaxurl' => admin_url('admin-ajax.php'),
		       //  'security' => wp_create_nonce('load_journey_book'),
	        // ));
		}

		/**
		 * init_styles
		 */
		public function init_styles() {
			wp_enqueue_style( 'leaflet-styles', plugins_url( '/assets/js/leaflet/leaflet.css', __FILE__ ) );
			
			wp_register_style( 'map-explorer-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );
			wp_enqueue_style( 'map-explorer-styles' );
		}

	}

	Map_Explorer::instance();

}