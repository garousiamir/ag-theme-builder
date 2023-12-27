<?php
/**
 * Ag_Theme_Compatibility setup
 *
 * @package ag-theme-builder
 */

/**
 * Ag theme compatibility.
 */
class Ag_Theme_Compatibility {

	/**
	 * Instance of Ag_Theme_Compatibility.
	 *
	 * @var Ag_Theme_Compatibility
	 */
	private static $instance;

	/**
	 *  Initiator
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Ag_Theme_Compatibility();

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

		$header_meta = ag_get_meta( 'ag-main-header-display' );
		$footer_meta = ag_get_meta( 'ag-footer-layout' );

		if ( ag_theme_builder_header_enabled() && 'disabled' !== $header_meta ) {
			remove_action( 'ag_header', 'ag_construct_header' );
			add_action( 'ag_header', 'ag_theme_builder_render_header' );
		}

		if ( ag_theme_builder_footer_enabled() && 'disabled' !== $footer_meta ) {
			remove_action( 'ag_footer', 'ag_construct_footer' );
			add_action( 'ag_footer', 'ag_theme_builder_render_footer' );
		}

		if ( ag_theme_builder_is_singular_enabled() ) {
			remove_action( 'ag_content_before', 'ag_construct_content_before' );
			remove_action( 'ag_content_after', 'ag_construct_content_after' );
			remove_action( 'ag_title_wrapper', 'ag_construct_title_wrapper' );
			remove_action( 'ag_content_loop', 'ag_construct_content_loop' );
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

			remove_action( 'ag_content_before', 'ag_construct_content_before' );
			remove_action( 'ag_content_after', 'ag_construct_content_after' );
			remove_action( 'ag_title_wrapper', 'ag_construct_title_wrapper' );
			remove_action( 'ag_content_loop', 'ag_construct_content_loop' );
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

Ag_Theme_Compatibility::instance();
