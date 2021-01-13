<?php
namespace Map_Explorer_Elementor_Extensions;


final class Map_Explorer_Elementor_Extensions {

/**
	 * Plugin Version
	 *
	 * @since 1.0.0
	 *
	 * @var string The plugin version.
	 */
	const VERSION = '1.0.0';

	/**
	 * Minimum Elementor Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';

	/**
	 * Minimum PHP Version
	 *
	 * @since 1.0.0
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '7.0';

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

		add_action( 'init', [ $this, 'i18n' ] );
		add_action( 'plugins_loaded', [ $this, 'init' ] );

	}

	/**
	 * Load Textdomain
	 *
	 * Load plugin localization files.
	 *
	 * Fired by `init` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function i18n() {

		load_plugin_textdomain( 'map-explorer-extensions' );

	}

	/**
	 * Initialize the plugin
	 *
	 * Load the plugin only after Elementor (and other plugins) are loaded.
	 * Checks for basic plugin requirements, if one check fail don't continue,
	 * if all check have passed load the files required to run the plugin.
	 *
	 * Fired by `plugins_loaded` action hook.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function init() {

		// Check if Elementor installed and activated
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_main_plugin' ] );
			return;
		}

		// Check for required Elementor version
		if ( ! version_compare( ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_elementor_version' ] );
			return;
		}

		// Check for required PHP version
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_minimum_php_version' ] );
			return;
		}


		add_action( 'wp_head', [ $this, 'add_inline_script' ] );
		

		add_action( 'init', [ $this, 'register_shortcodes' ] );
		

		// Add Plugin actions
		add_action( 'elementor/frontend/before_enqueue_scripts', [ $this, 'init_scripts' ] );
		add_action( 'elementor/frontend/after_enqueue_styles', [ $this, 'init_styles' ] );


		add_action( 'elementor/widgets/widgets_registered', [ $this, 'init_widgets' ] );

		//add_action( 'elementor/controls/controls_registered', [ $this, 'init_controls' ] );


	}

	/**
	 * Add Inline Script
	 */
	public function add_inline_script() {
	  echo '<script>var map_locations=[];</script>';
	}


	/**
	 * Register Shortcodes
	 */
	public function register_shortcodes() {

		// register map_locations shortcode
		add_shortcode( 'map_explorer', [ $this, 'add_map_explorer_shortcode' ] ); 

		// register map_locations shortcode
		add_shortcode( 'map_locations', [ $this, 'add_map_locations_shortcode' ] ); 

		// register map_locations shortcode
		add_shortcode( 'map_location', [ $this, 'add_map_location_shortcode' ] ); 

	}

	/**
	 * Show map_locations shortcode
	 */
	public function add_map_explorer_shortcode( $atts, $content = "" ) {
		return '<div class="map-wrapper"><div id="map_explorer"></div></div>';
	}

	/**
	 * Show map_locations shortcode
	 */
	public function add_map_locations_shortcode( $atts, $content = "" ) {
		return $this->get_locations();
	}

	/**
	 * Create GeoJSON from Posts
	 */
	public function get_locations() {	
		$args = [
			'post_type' => 'timeline',
			'post_status' => 'any',
			'numberposts' => -1,
		];
		$posts = get_posts($args);


		$geojsons = [];

		foreach( $posts as $post ) {

			$geojson = $this->get_geojson_from_post($post);

			if($geojson) $geojsons[] = $geojson; 

		}

		return '<script type="text/javascript">var map_locations = '.json_encode($geojsons).';</script>';
	}

	public function get_geojson_from_post($post) {

		$lat = get_post_meta( $post->ID, 'map_latitude', true );
		$lng = get_post_meta( $post->ID, 'map_longitude', true );

		if( $lat && !empty($lat) && $lng && !empty($lng) ) {

			$geojson = [
				'type' => 'Feature',
				'geometry' => [
					'type' => 'Point',
					'coordinates' => [(float)$lng, (float)$lat]
				],
				'properties' => [
					'id' => $post->ID,
					'name' => $post->post_title,
					'url' => 'location-'.$post->ID,
					//'image' => $post->get_featured_image(),
				]
			];

			return $geojson;

		}

		return;

	}

	/**
	 * Show map_locations shortcode
	 */
	public function add_map_location_shortcode( $atts, $content = "" ) {
		global $post;

		$geojson = $this->get_geojson_from_post($post);

		return '<script type="text/javascript">map_locations.push('.json_encode($geojson).');</script>';
	}

	/**
	 * init_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init_scripts() {

		wp_enqueue_script( 
			'leaflet', 
			plugins_url( '/assets/js/bundle.js', __FILE__ ), 
			false, 
			false, 
			true 
		);
		
		wp_enqueue_script( 
			'map-explorer', 
			plugins_url( '/assets/js/map-explorer.js', __FILE__ ), 
			[
				'jquery',
				'leaflet',
			], 
			false, 
			true 
		);
	}

	/**
	 * init_styles
	 *
	 * Load required css files.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init_styles() {

		wp_enqueue_style( 'leaflet-styles', plugins_url( '/assets/css/bundle.css', __FILE__ ) );

		wp_register_style( 'map-explorer-styles', plugins_url( '/assets/css/style.css', __FILE__ ) );
		wp_enqueue_style( 'map-explorer-styles' );

	}


	/**
	 * init_widgets
	 *
	 * Loads widgets
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init_widgets() {

		require_once( plugin_dir_path( __FILE__ ) . 'widgets/map-explorer.php' );
		\Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Map_Explorer() );

	}

	public function init_controls() {

		// require_once( plugin_dir_path( __FILE__ ) . 'includes/cc-core.php' );
		// new \Elementor\Pavilions_Core();

	}

	public function add_elementor_skins( $widget ) {

		// require_once( plugin_dir_path( __FILE__ ) . '/skins/skin-bookshelf.php' );
		// $widget->add_skin( new \ElementorPro\Modules\Posts\Skins\Skin_Bookshelf( $widget ) );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have Elementor installed or activated.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_missing_main_plugin() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'map-explorer-extensions' ),
			'<strong>' . esc_html__( 'Elementor Test Extension', 'map-explorer-extensions' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'map-explorer-extensions' ) . '</strong>'
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required Elementor version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_elementor_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'map-explorer-extensions' ),
			'<strong>' . esc_html__( 'Elementor Test Extension', 'map-explorer-extensions' ) . '</strong>',
			'<strong>' . esc_html__( 'Elementor', 'map-explorer-extensions' ) . '</strong>',
			 self::MINIMUM_ELEMENTOR_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}

	/**
	 * Admin notice
	 *
	 * Warning when the site doesn't have a minimum required PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function admin_notice_minimum_php_version() {

		if ( isset( $_GET['activate'] ) ) unset( $_GET['activate'] );

		$message = sprintf(
			/* translators: 1: Plugin name 2: PHP 3: Required PHP version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'map-explorer-extensions' ),
			'<strong>' . esc_html__( 'Elementor Test Extension', 'map-explorer-extensions' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'map-explorer-extensions' ) . '</strong>',
			 self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );

	}


}
Map_Explorer_Elementor_Extensions	::instance();