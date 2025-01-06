<?php

namespace Portfolio;

const ASSETS_DIR  = '/assets';
const BUILD_DIR  = ASSETS_DIR . '/build';
const STYLES_DIR  = BUILD_DIR . '/css';

/**
 * Enqueues style.css on the front.
 *
 * @return void
 */
function enqueue_styles(): void {
	wp_enqueue_style(
		'portfolio-style',
		get_parent_theme_file_uri( STYLES_DIR . '/main.css' ),
		array(),
		wp_get_theme()->get( 'Version' )
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_styles' );

/**
 * Load custom block styles only when the block is used.
 *
 * @return void
 */
function enqueue_custom_block_styles(): void {
	// Scan our styles folder to locate block styles.
	$files = glob( get_template_directory() . STYLES_DIR . '/blocks/core/*.css' );

	foreach ( $files as $file ) {

		// Get the filename and core block name.
		$filename   = basename( $file, '.css' );
		$block_name = "core/$filename";

		$asset = include get_theme_file_path( STYLES_DIR . "/blocks/core/{$filename}.asset.php" );

		wp_enqueue_block_style(
			$block_name,
			array(
				'handle' => "portfolio-{$filename}-block-style",
				'src'    => get_theme_file_uri( STYLES_DIR . "/blocks/core/{$filename}.css" ),
				'path'   => get_theme_file_path( STYLES_DIR . "/blocks/core/{$filename}.css" ),
				'deps'   => $asset['dependencies'],
				'ver'    => $asset['version'],
			)
		);
	}
}
add_action( 'after_setup_theme', __NAMESPACE__ . '\enqueue_custom_block_styles' );