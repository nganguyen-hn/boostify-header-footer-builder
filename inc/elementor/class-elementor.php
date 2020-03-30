<?php
/**
 * Class Header Footer Elementor
 *
 * Main Plugin class
 * @since 1.2.0
 */

namespace Boostify_Header_Footer;


class Elementor {
	/**
	 * Instance
	 *
	 * @since 1.2.0
	 * @access private
	 * @static
	 *
	 * @var Plugin The single instance of the class.
	 */
	private static $instance = null;


	private $modules_manager;
	/**
	 * Instance
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return Plugin An instance of the class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Register custom widget categories.
	 */
	public function add_elementor_widget_categories( $elements_manager ) {
		$elements_manager->add_category(
			'ht_hf_builder',
			array(
				'title' => esc_html__( 'Boostify Header Footer', 'boostify' ),
			)
		);
	}

	public function get_widgets() {
		$widgets = array(
			'Site_Logo',
			'Site_Search',
			'Navigation',
			'Mega_Menu',
			'Copyright',
			'Image_Retina',
		);

		if ( class_exists( 'Woocommerce' ) ) {
			array_push( $widgets, 'Cart_Icon' );
		}

		return $widgets;
	}

	/**
	 * widget_scripts
	 *
	 * Load required plugin core files.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function widget_scripts() {
		wp_register_script(
			'boostify_hf_nav_menu',
			BOOSTIFY_HEADER_FOOTER_URL . 'assets/js/nav-menu' . boostify_header_footer_suffix() . '.js',
			array( 'jquery' ),
			BOOSTIFY_HEADER_FOOTER_VER,
			true
		);

		// Search
		wp_register_script(
			'boostify_hf_search',
			BOOSTIFY_HEADER_FOOTER_URL . 'assets/js/search' . boostify_header_footer_suffix() . '.js',
			array( 'jquery' ),
			BOOSTIFY_HEADER_FOOTER_VER,
			true
		);

		// Mega Menu
		wp_register_script(
			'boostify_hf_nav_mega',
			BOOSTIFY_HEADER_FOOTER_URL . 'assets/js/mega-menu' . boostify_header_footer_suffix() . '.js',
			array( 'jquery' ),
			BOOSTIFY_HEADER_FOOTER_VER,
			true
		);
	}
	/**
	 * Include Widgets files
	 *
	 * Load widgets files
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function autoload_widgets( $classname ) {
		if ( ! class_exists( $classname ) ) {
			$filename = strtolower(
				preg_replace(
					array(
						'/^' . __NAMESPACE__ . '\\\/',
						'/([a-z])([A-Z])/',
						'/_/',
						'/\\\/',
					),
					array(
						'',
						'$1-$2',
						'-',
						DIRECTORY_SEPARATOR,
					),
					$classname
				)
			);
			$filename = str_replace( 'widgets\\', '', $filename );
			$filename = BOOSTIFY_HEADER_FOOTER_PATH . 'inc/elementor/widgets/class-' . $filename . '.php';

			if ( is_readable( $filename ) ) {
				include $filename;
			}
		}
	}

	/**
	 * Register Widgets
	 *
	 * Register new Elementor widgets.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init_widgets() {
		// Its is now safe to include Widgets files
		$widget_manager = \Elementor\Plugin::instance()->widgets_manager;
		foreach ( $this->get_widgets() as $widget ) {
			$class_name = 'Boostify_Header_Footer\Widgets\\' . $widget;

			$widget_manager->register_widget_type( new $class_name() );
		}
	}

	public function init() {

		$this->modules_manager = \Boostify_Header_Footer\Module\Sticky::instance();

		$elementor = \Elementor\Plugin::$instance;

		// Add element category in panel
		$elementor->elements_manager->add_category(
			'boostify-sticky',
			array(
				'title' => __( 'Header Sticky', 'boostify' ),
				'icon'  => 'font',
			),
			1
		);

		do_action( 'elementor_controls/init' ); // phpcs:ignore
	}

	private function setup_hooks() {
		// Register Module.
		add_action( 'elementor/init', array( $this, 'init' ) );
		add_action( 'elementor/init', array( $this, 'register_abstract' ) );
		// Register custom widget categories.
		add_action( 'elementor/elements/categories_registered', array( $this, 'add_elementor_widget_categories' ) );
		// Register widget scripts
		add_action( 'elementor/frontend/after_register_scripts', array( $this, 'widget_scripts' ) );
		// Register widgets
		add_action( 'elementor/widgets/widgets_registered', array( $this, 'init_widgets' ) );
	}

	public function register_abstract() {
		require BOOSTIFY_HEADER_FOOTER_PATH . 'inc/elementor/abstract/class-base-widget.php';
		require BOOSTIFY_HEADER_FOOTER_PATH . 'inc/elementor/abstract/class-nav-menu.php';
	}

	public function includes() {
		require BOOSTIFY_HEADER_FOOTER_PATH . 'inc/elementor/module/class-sticky.php';
	}

	/**
	 *  Plugin class constructor
	 *
	 * Register plugin action hooks and filters
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {
		spl_autoload_register( array( $this, 'autoload_widgets' ) );
		$this->includes();

		$this->setup_hooks();
	}
}
// Instantiate Boostify_Header_Footer_Elementor Class
Elementor::instance();

