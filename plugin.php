<?php

use Elementor\Core\Files\CSS\Post;
use Elementor\Frontend;
use Elementor\Post_CSS_File;
use ElementorPro\Plugin;
use Ag_Theme_Builder\Lib\Ag_Target_Rules_Fields;


/**
 * Class Ag_Theme_Builder_Main
 */
class Ag_Theme_Builder_Main {

	/**
	 * Instance of Ag_Theme_Builder_Main
	 *
	 * @var Ag_Theme_Builder_Main
	 */
	private static $instance = null;

	/**
	 * Instance of Elementor Frontend class.
	 *
	 * @var Frontend()
	 */
	private static $elementor_instance;
	/**
	 * Current theme template
	 *
	 * @var String
	 */
	public $template;

	/**
	 * Instance of Ag_Theme_Builder_Main
	 *
	 * @return Ag_Theme_Builder_Main Instance of Ag_Theme_Builder_Main
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	public function init() {

		$this->template = get_template();

		self::$elementor_instance = ( defined( 'ELEMENTOR_VERSION' ) && is_callable( 'Elementor\Plugin::instance' ) ) ? Elementor\Plugin::instance() : '';

		if ( self::$elementor_instance ) {

			$this->includes();

			if ( 'ag' === $this->template ) {
				require AG_THEME_BUILDER_DIR . 'themes/class-ag-compatibility.php';
			} elseif ( 'astra' === $this->template ) {
				require AG_THEME_BUILDER_DIR . 'themes/class-astra-compatibility.php';
			} elseif ( 'generatepress' === $this->template ) {
				require AG_THEME_BUILDER_DIR . 'themes/class-generatepress-compatibility.php';
			} elseif ( 'megaone' === $this->template ) {
				require AG_THEME_BUILDER_DIR . 'themes/class-megaone-compatibility.php';
			} elseif ( 'oceanwp' === $this->template ) {
				require AG_THEME_BUILDER_DIR . 'themes/class-oceanwp-compatibility.php';
			} else {
				require AG_THEME_BUILDER_DIR . 'themes/class-default-compatibility.php';
			}

			// Scripts and styles.
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
			add_filter( 'body_class', array( $this, 'body_class' ) );

	

			add_shortcode( 'ag_theme_builder_template', array( $this, 'render_template' ) );

			//Add Elementor Modules
			add_action( 'init', array( $this, 'register_modules' ) );

			// Register Document Type
			add_action( 'elementor/documents/register', array( $this, 'register_elementor_document_type' ) );

			//Comment Shortcode
			add_shortcode( 'ag_comments_template', array( $this, 'ag_theme_builder_comments_template' ) );

		}

	}

	/**
	 * Loads the globally required files for the plugin.
	 */
	public function includes() {

		require_once AG_THEME_BUILDER_DIR . 'admin/class-ag-admin.php';
		require_once AG_THEME_BUILDER_DIR . 'admin/class-ag-rest-api.php';

		require_once AG_THEME_BUILDER_DIR . 'inc/ag-functions.php';

		// Load Target rules.
		require_once AG_THEME_BUILDER_DIR . 'lib/target-rule/class-ag-target-rules-fields.php';

		// Load WPML & Polylang Compatibility if WPML is installed and activated.
		if ( defined( 'ICL_SITEPRESS_VERSION' ) || defined( 'POLYLANG_BASENAME' ) ) {
			require_once AG_THEME_BUILDER_DIR . 'inc/wpml-compatibility.php';
		}

	}

	/**
	 * Prints the Header content.
	 */
	public static function get_header_content() {
		if ( self::$elementor_instance ) {
			echo self::$elementor_instance->frontend->get_builder_content_for_display( get_ag_theme_builder_header_id() ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Prints the Footer content.
	 */
	public static function get_footer_content() {
		if ( self::$elementor_instance ) {
			echo self::$elementor_instance->frontend->get_builder_content_for_display( get_ag_theme_builder_footer_id() ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Prints the Before Footer content.
	 */
	public static function get_singular_content() {
		if ( self::$elementor_instance ) {
			echo self::$elementor_instance->frontend->get_builder_content_for_display( ag_theme_builder_get_singular_id() ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Prints the Before Footer content.
	 */
	public static function get_archive_content() {
		if ( self::$elementor_instance ) {
			echo self::$elementor_instance->frontend->get_builder_content_for_display( ag_theme_builder_get_archive_id() ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Get option for the plugin settings
	 *
	 * @param mixed $setting Option name.
	 * @param mixed $default Default value to be received if the option value is not stored in the option.
	 *
	 * @return mixed.
	 */
	public static function get_settings( $setting = '', $default = '' ) {
		if ( 'type_header' === $setting || 'type_footer' === $setting || 'type_singular' === $setting || 'type_archive' === $setting ) {
			$templates = self::get_template_id( $setting );
			$template  = ! is_array( $templates ) ? $templates : $templates[0];
			$template  = apply_filters( "ag_theme_builder_get_settings_{$setting}", $template );
			return $template;
		}
	}

	/**
	 * Get header or footer template id based on the meta query.
	 *
	 * @param String $type Type of the template header/footer.
	 *
	 * @return Mixed       Returns the header or footer template id if found, else returns string ''.
	 */
	public static function get_template_id( $type ) {

		$option = array(
			'location'  => 'ag_theme_builder_target_include_locations',
			'exclusion' => 'ag_theme_builder_target_exclude_locations',
			'users'     => 'ag_theme_builder_target_user_roles',
		);

		$ag_theme_builder_templates = Ag_Target_Rules_Fields::get_instance()->get_posts_by_conditions( 'ag-themer', $option );

		foreach ( $ag_theme_builder_templates as $template ) {
			if ( get_post_meta( absint( $template['id'] ), 'ag_theme_builder_template_type', true ) === $type ) {
				return $template['id'];
			}
		}

		return '';
	}

	/**
	 * Enqueue styles and scripts.
	 */
	public function enqueue_scripts() {

		$all_modules    = Ag_Elementor_Module_List::instance()->get_list();
		$active_modules = Ag_Elementor_Dashboard::instance()->utils->get_option( 'ag_elementor_module_list', array_keys( $all_modules ) );

		wp_enqueue_style( 'ag-theme-builder', AG_THEME_BUILDER_URL . 'assets/css/ag-theme-builder.css', null, AG_THEME_BUILDER_VER );
		wp_enqueue_script( 'ag-theme-builder', AG_THEME_BUILDER_URL . 'assets/js/ag-theme-builder.js', array( 'jquery' ), AG_THEME_BUILDER_VER, true );

		wp_script_add_data( 'ag-theme-builder', 'async', true );

		//Frontend Panel
		if ( is_user_logged_in() && current_user_can( 'edit_posts' ) && is_array( $active_modules ) && in_array( 'theme-builder', $active_modules, true ) && ! ( self::$elementor_instance && \Elementor\Plugin::$instance->preview->is_preview_mode() ) ) {

			global $user_ID;

			wp_enqueue_style( 'ag-theme-builder-frontend', AG_THEME_BUILDER_URL . 'admin/assets/css/ag-frontend.css', array(), AG_THEME_BUILDER_VER );
			wp_enqueue_script( 'ag-theme-builder-frontend', AG_THEME_BUILDER_URL . 'admin/assets/js/ag-frontend.js', array(), AG_THEME_BUILDER_VER, true );
			wp_enqueue_script( 'ag-theme-builder-frontend-chunk', AG_THEME_BUILDER_URL . 'admin/assets/js/ag-chunk.js', array(), AG_THEME_BUILDER_VER, true );
			wp_enqueue_script( 'ag-theme-builder-frontend-main', AG_THEME_BUILDER_URL . 'admin/assets/js/ag-main.js', array(), AG_THEME_BUILDER_VER, true );
			wp_localize_script(
				'ag-theme-builder-frontend',
				'AgThemeBuilderApi',
				array(
					'ApiUrl'   => get_rest_url(),
					'siteUrl'  => get_site_url(),
					'adminUrl' => get_admin_url(),
					'user'     => $user_ID,
					'nonce'    => wp_create_nonce( 'wp_rest' ),
				)
			);
		}

		if ( class_exists( '\Elementor\Plugin' ) ) {
			$elementor = \Elementor\Plugin::instance();
			$elementor->frontend->enqueue_styles();
		}

		if ( class_exists( '\ElementorPro\Plugin' ) ) {
			$elementor_pro = Plugin::instance();
			$elementor_pro->enqueue_styles();
		}

		if ( self::$elementor_instance && ag_theme_builder_header_enabled() ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new Post( get_ag_theme_builder_header_id() );
			} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
				$css_file = new Post_CSS_File( get_ag_theme_builder_header_id() );
			}

			$css_file->enqueue();
		}

		if ( self::$elementor_instance && ag_theme_builder_footer_enabled() ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new Post( get_ag_theme_builder_footer_id() );
			} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
				$css_file = new Post_CSS_File( get_ag_theme_builder_footer_id() );
			}

			$css_file->enqueue();
		}

		if ( self::$elementor_instance && ag_theme_builder_is_singular_enabled() ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new Post( ag_theme_builder_get_singular_id() );
			} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
				$css_file = new Post_CSS_File( ag_theme_builder_get_singular_id() );
			}
			$css_file->enqueue();
		}

		if ( self::$elementor_instance && ag_theme_builder_is_archive_enabled() ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new Post( ag_theme_builder_get_archive_id() );
			} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
				$css_file = new Post_CSS_File( ag_theme_builder_get_archive_id() );
			}
			$css_file->enqueue();
		}

	}

	/**
	 * Load admin styles on header footer elementor edit screen.
	 */
	public function enqueue_admin_scripts() {

		global $pagenow;
		$screen = get_current_screen();

		wp_enqueue_style( 'ag-theme-builder-admin', AG_THEME_BUILDER_URL . 'admin/assets/css/ag-admin.css', array(), AG_THEME_BUILDER_VER );

		if ( ( 'ag-themer' === $screen->id && ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) ) || ( 'edit.php' === $pagenow && 'edit-ag-themer' === $screen->id ) ) {

			wp_enqueue_script( 'ag-theme-builder-admin', AG_THEME_BUILDER_URL . 'admin/assets/js/ag-admin.js', array( 'jquery' ), AG_THEME_BUILDER_VER, true );

		}
	}

	/**
	 * Adds classes to the body tag conditionally.
	 *
	 * @param Array $classes array with class names for the body tag.
	 *
	 * @return Array          array with class names for the body tag.
	 */
	public function body_class( $classes ) {

		$classes[] = 'ag-theme-builder-template';

		return $classes;
	}

	/**
	 * Callback to shortcode.
	 *
	 * @param array $atts attributes for shortcode.
	 */
	public function render_template( $atts ) {

		$atts = shortcode_atts(
			array(
				'id' => '',
			),
			$atts,
			'ag_theme_builder_template'
		);

		$id = ! empty( $atts['id'] ) ? apply_filters( 'ag_theme_builder_render_template_id', intval( $atts['id'] ) ) : '';

		if ( empty( $id ) ) {
			return '';
		}

		if ( self::$elementor_instance ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				$css_file = new Post( $id );
			} elseif ( class_exists( '\Elementor\Post_CSS_File' ) ) {
				// Load elementor styles.
				$css_file = new Post_CSS_File( $id );
			}
			$css_file->enqueue();
		}

		if ( self::$elementor_instance ) {
			return self::$elementor_instance->frontend->get_builder_content_for_display( $id );
		}

	}





	/**
	 * Register Modules
	 *
	 * Register Modules Settings.
	 *
	 * @since 1.0.0
	 * @access public
	 */

	public function register_modules() {
		include_once AG_THEME_BUILDER_DIR . '/inc/header-sticky.php';
	}

	/**
	 * Register Document Type
	 *
	 * Register Modules Settings.
	 *
	 * @since 1.0.0
	 * @access public
	 */

	public function register_elementor_document_type( $documents_manager ) {
		if ( get_post_type() === 'ag-themer' ) {
			update_post_meta( get_the_ID(), '_elementor_template_type', 'ag-themer' );
		}
		include_once AG_THEME_BUILDER_DIR . '/inc/preview-settings.php';
		$documents_manager->register_document_type( Ag_Theme_Builder_Settings::get_type(), Ag_Theme_Builder_Settings::get_class_full_name() );
	}

	/**
	 * Create Shortcode for Comment
	 *
	 * @since 1.0.0
	 * @access public
	 */

	public function ag_theme_builder_comments_template() {
		if ( ( comments_open() || get_comments_number() ) ) {
			comments_template();
		}
	}



}

// Instantiate Ag_Theme_Builder_Main Class
Ag_Theme_Builder_Main::instance();
