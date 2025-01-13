<?php

namespace Portfolio;

const ASSETS_DIR  = '/assets';
const BUILD_DIR  = ASSETS_DIR . '/build';
const STYLES_DIR  = BUILD_DIR . '/css';
const SCRIPTS_DIR  = BUILD_DIR . '/js';
const VENDOR_DIR  = ASSETS_DIR . '/vendor';

/**
 * Enqueues style.css on the front.
 *
 * @return void
 */
function enqueue_theme_assets(): void {
	wp_enqueue_style(
		'portfolio-style',
		get_parent_theme_file_uri( STYLES_DIR . '/main.css' ),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	wp_enqueue_script(
		'portfolio-script',
		get_parent_theme_file_uri( SCRIPTS_DIR . '/site.js' ),
		array(),
		wp_get_theme()->get( 'Version' )
	);

	// Block scripts
	$files = glob( get_template_directory() . SCRIPTS_DIR . '/blocks/*.js' );

	foreach ( $files as $file ) {

		// Get the filename and core block name.
		$filename   = basename( $file, '.js' );
		$block_name = "core/$filename";

		$asset = include get_theme_file_path( SCRIPTS_DIR . "/blocks/{$filename}.asset.php" );

		wp_register_script(
			"portfolio-{$block_name}-block-script",
			get_parent_theme_file_uri( SCRIPTS_DIR . "/blocks/{$filename}.js" ),
			$asset['dependencies'],
			$asset['version'],
		);
	}
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_theme_assets' );

/**
 * Load custom block styles only when the block is used.
 *
 * @return void
 */
function theme_setup(): void {
	add_theme_support( 'editor-styles' );

	add_editor_style( STYLES_DIR . '/editor.css' );

	// Remove core block patterns.
	remove_theme_support( 'core-block-patterns' );

	// Scan our styles folder to locate block styles.
	$files = glob( get_template_directory() . STYLES_DIR . '/blocks/*.css' );

	foreach ( $files as $file ) {

		// Get the filename and core block name.
		$filename   = basename( $file, '.css' );
		$block_name = "core/$filename";

		$asset = include get_theme_file_path( STYLES_DIR . "/blocks/{$filename}.asset.php" );

		wp_enqueue_block_style(
			$block_name,
			array(
				'handle' => "portfolio-{$filename}-block-style",
				'src'    => get_theme_file_uri( STYLES_DIR . "/blocks/{$filename}.css" ),
				'path'   => get_theme_file_path( STYLES_DIR . "/blocks/{$filename}.css" ),
				'deps'   => $asset['dependencies'],
				'ver'    => $asset['version'],
			)
		);
	}
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\theme_setup' );

function enqueue_block_editor_modifications(): void {
	// Scan our styles folder to locate block styles.
	$files = glob( get_template_directory() . SCRIPTS_DIR . '/editor/*.js' );

	foreach ( $files as $file ) {

		// Get the filename and core block name.
		$filename   = basename( $file, '.js' );
		$block_name = "core/$filename";

		$asset = include get_theme_file_path( SCRIPTS_DIR . "/editor/{$filename}.asset.php" );

		wp_enqueue_script(
			"portfolio-{$block_name}-block-modifications",
			get_parent_theme_file_uri( SCRIPTS_DIR . "/editor/{$filename}.js" ),
			$asset['dependencies'],
			$asset['version'],
		);

	}
}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_modifications' );

function enqueue_custom_block_scripts( $block_content, $block ) {
	$files = glob( get_template_directory() . SCRIPTS_DIR . '/blocks/*.js' );

	foreach ( $files as $file ) {

		// Get the filename and core block name.
		$filename   = basename( $file, '.js' );
		$block_name = "core/$filename";

		wp_enqueue_script( "portfolio-{$block_name}-block-script" );
	}

	return $block_content;
}
add_filter( 'render_block', __NAMESPACE__ . '\enqueue_custom_block_scripts', 10, 2 );

function filter_heading_block_render( $block_content, $block ) {
	if ( isset( $block['attrs']['isInteractive'] ) ) {

	}

	return $block_content;
}
add_filter( 'render_block_core/heading', __NAMESPACE__ . '\filter_heading_block_render', 10, 2 );

function filter_button_block_render( $block_content, $block ) {
	if ( isset( $block['attrs']['isCircular'] ) ) {
		$tags = new \WP_HTML_Tag_Processor( $block_content );

		if ( $tags->next_tag( array( 'class_name' => 'wp-block-button' ) ) ) {
			$tags->add_class( 'is-circular' );
		}

		$block_content = $tags->get_updated_html();
	}

	return $block_content;
}
add_filter( 'render_block_core/button', __NAMESPACE__ . '\filter_button_block_render', 10, 2 );

function filter_post_template_block_render( $block_content, $block ) {
	if ( isset( $block['attrs']['isAlternatingColumns'] ) ) {
		$tags = new \WP_HTML_Tag_Processor( $block_content );

		if ( $tags->next_tag( array( 'class_name' => 'wp-block-post-template' ) ) ) {
			$tags->add_class( 'is-alternating-columns' );
		}

		$block_content = $tags->get_updated_html();
	}

	return $block_content;
}
add_filter( 'render_block_core/post-template', __NAMESPACE__ . '\filter_post_template_block_render', 10, 2 );

function site_symbols() {
	?>
	<svg id="superguideIcons" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" style="position: absolute; width: 0; height: 0; overflow: hidden;">
		<symbol id="icon_asterisk" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M12.444 28V17.733l-7.233 7.273-2.217-2.217 7.273-7.233H0v-3.112h10.267L2.994 5.211l2.217-2.217 7.233 7.273V0h3.112v10.267l7.233-7.273 2.217 2.217-7.273 7.233H28v3.112H17.733l7.273 7.233-2.217 2.217-7.233-7.273V28h-3.112Z" fill="currentColor"/>
		</symbol>
	</svg>
	<?php
}
add_action( 'wp_footer', __NAMESPACE__ . '\site_symbols' );
