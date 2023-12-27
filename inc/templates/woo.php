<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

if ( ag_theme_builder_is_singular_enabled() ) {
	ag_theme_builder_render_singular();
} elseif ( ag_theme_builder_is_archive_enabled() ) {
	ag_theme_builder_render_archive();
}

get_footer( 'shop' );
