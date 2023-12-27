<?php
/**
 * Ag_Default_Compatibility setup
 *
 * @package ag-theme-builder
 */

/**
 * Ag theme compatibility.
 */
class Ag_Default_Compatibility {

	/**
	 * Instance of Ag_Default_Compatibility.
	 *
	 * @var Ag_Default_Compatibility
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Ag_Default_Compatibility();

			add_action( 'wp', array( self::$instance, 'hooks' ) );
		}

		return self::$instance;
	}

	/**
	 * Run all the Actions / Filters.
	 */
	public function hooks() {

		if ( get_post_type() === 'ag-themer' || ( \Elementor\Plugin::$instance->preview->is_preview_mode() && ag_theme_builder_is_singular_enabled() ) ) {
			add_filter( 'single_template', array( $this, 'blank_template' ) );
			return;
		}

		if ( ag_theme_builder_header_enabled() ) {
			add_action( 'get_header', array( $this, 'override_header' ) );
			add_action( 'ag_header', 'ag_theme_builder_render_header' );
		}

		if ( ag_theme_builder_footer_enabled() ) {
			add_action( 'get_footer', array( $this, 'override_footer' ) );
			add_action( 'ag_footer', 'ag_theme_builder_render_footer' );
		}

		if ( ag_theme_builder_is_singular_enabled() ) {

			add_filter( 'page_template', array( $this, 'empty_template' ) );
			add_filter( 'single_template', array( $this, 'empty_template' ) );
			add_filter( '404_template', array( $this, 'empty_template' ) );
			add_filter( 'frontpage_template', array( $this, 'empty_template' ) );

			if ( defined( 'WOOCOMMERCE_VERSION' ) && ( is_product() || is_cart() || is_checkout() || is_account_page() ) ) {
				add_action( 'template_redirect', array( $this, 'woo_template' ), 999 );
				add_action( 'template_include', array( $this, 'woo_template' ), 999 );
			}
		}

		if ( ag_theme_builder_is_archive_enabled() ) {

			add_filter( 'search_template', array( $this, 'empty_template' ) );
			add_filter( 'date_template', array( $this, 'empty_template' ) );
			add_filter( 'author_template', array( $this, 'empty_template' ) );
			add_filter( 'archive_template', array( $this, 'empty_template' ) );
			add_filter( 'category_template', array( $this, 'empty_template' ) );
			add_filter( 'tag_template', array( $this, 'empty_template' ) );
			add_filter( 'home_template', array( $this, 'empty_template' ) );

			if ( defined( 'WOOCOMMERCE_VERSION' ) && is_shop() || ( is_tax( 'product_cat' ) && is_product_category() ) || ( is_tax( 'product_tag' ) && is_product_tag() ) ) {
				add_action( 'template_redirect', array( $this, 'woo_template' ), 999 );
				add_action( 'template_include', array( $this, 'woo_template' ), 999 );
			}
		}

	}


	/**
	 * Function for overriding the header in the elmentor way.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function override_header() {
		require AG_THEME_BUILDER_DIR . 'inc/templates/header.php';
		$templates   = array();
		$templates[] = 'header.php';
		// Avoid running wp_head hooks again.
		remove_all_actions( 'wp_head' );
		ob_start();
		locate_template( $templates, true );
		ob_get_clean();
	}

	/**
	 * Function for overriding the footer in the elmentor way.
	 *
	 * @return void
	 * @since 1.0.0
	 *
	 */
	public function override_footer() {
		require AG_THEME_BUILDER_DIR . 'inc/templates/footer.php';
		$templates   = array();
		$templates[] = 'footer.php';
		// Avoid running wp_footer hooks again.
		remove_all_actions( 'wp_footer' );
		ob_start();
		locate_template( $templates, true );
		ob_get_clean();
	}

	public function blank_template( $template ) {

		global $post;

		if ( file_exists( AG_THEME_BUILDER_DIR . 'inc/templates/blank.php' ) ) {
			return AG_THEME_BUILDER_DIR . 'inc/templates/blank.php';
		}

		return $template;
	}

	public function empty_template( $template ) {

		if ( file_exists( AG_THEME_BUILDER_DIR . 'inc/templates/empty.php' ) ) {
			return AG_THEME_BUILDER_DIR . 'inc/templates/empty.php';
		}

		return $template;
	}

	public function woo_template( $template ) {
		if ( file_exists( AG_THEME_BUILDER_DIR . 'inc/templates/woo.php' ) ) {
			return AG_THEME_BUILDER_DIR . 'inc/templates/woo.php';
		}

		return $template;

	}

}

Ag_Default_Compatibility::instance();
