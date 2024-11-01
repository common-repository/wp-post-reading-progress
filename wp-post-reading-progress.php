<?php
/**
 * Plugin Name:     WP Post Reading Progress
 * Plugin URI:      https://plugins.kul.site
 * Description:     Show the progress on reading a page or a post on a WordPress based website.
 * Author:          Alexandru Negoita
 * Author URI:      https://kul.site
 * Text Domain:     wp-post-reading-progress
 * Domain Path:     /languages
 * Version:         1.0.1
 *
 * @package         WP Post Reading Progress
 */

declare(strict_types=1);

namespace WP_POST_READING_PROGRESS;

use function WP_POST_READING_PROGRESS\HELPERS\enable_progress_bar_html;
use function WP_POST_READING_PROGRESS\HELPERS\get_wp_reading_progress_options;
use function WP_POST_READING_PROGRESS\HELPERS\progress_bar_is_allowed;

defined( 'ABSPATH' ) || exit;

/**
 * Get the Admin settings page.
 */
require_once 'helpers.php';
require_once 'admin/options.php';

/**
 * Get plugin data from the file headers.
 */
$plugin_version = \get_file_data(
	__FILE__,
	[
		'Version' => 'Version',
	],
	'plugin'
);

// Define allowed HTML tags to use.
// Used in the options pages from admin.
$kst_wp_progress_allowed_html = [
	'form'     => [
		'action'         => [],
		'accept'         => [],
		'accept-charset' => [],
		'enctype'        => [],
		'method'         => [],
		'name'           => [],
		'target'         => [],
	],
	'input'    => [
		'class'    => [],
		'id'       => [],
		'name'     => [],
		'value'    => [],
		'type'     => [],
		'step'     => [],
		'max'      => [],
		'min'      => [],
		'checked'  => [],
		'selected' => [],
	],
	'textarea' => [
		'class' => [],
		'id'    => [],
		'name'  => [],
		'value' => [],
		'cols'  => [],
		'rows'  => [],
	],
	'select'   => [
		'class' => [],
		'id'    => [],
		'name'  => [],
		'value' => [],
		'type'  => [],
	],
	'option'   => [
		'selected' => [],
		'value'    => [],
	],
	'a'        => [
		'href'  => [],
		'title' => [],
	],
	'em'       => [],
	'small'    => [],
	'span'     => [
		'class' => [],
		'id'    => [],
		'style' => [],
	],
	'p'        => [
		'class' => [],
		'id'    => [],
		'style' => [],
	],
	'div'      => [
		'class' => [],
		'id'    => [],
		'style' => [],
	],
	'label'    => [
		'for' => [],
	],
	'progress' => [],
];

// Declare the admin page variable for later use.
$plugin_admin_page = '';

if ( ! defined( __NAMESPACE__ . '\\VERSION' ) ) {
	define( __NAMESPACE__ . '\\VERSION', $plugin_version );
}

// Actions and filters.
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enque_front_scripts' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\enque_admin_scripts' );
add_filter( 'the_content', __NAMESPACE__ . '\\maybe_show_content_wp_reading_progress', 20 );

register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\remove_the_database_option' );

// Adding helpers.
require_once 'helpers.php';

/**
 * Enqueue the scripts and styles for the front-end.
 */
function enque_front_scripts() {

	wp_register_style(
		'wp-post-reading-progress',
		plugin_dir_url( __FILE__ ) . 'build/style.css',
		[],
		filemtime( plugin_dir_path( __FILE__ ) . 'build/style.css' )
	);

	wp_register_script(
		'wp-post-reading-progress',
		plugin_dir_url( __FILE__ ) . 'build/main.js',
		[],
		filemtime( plugin_dir_path( __FILE__ ) . 'build/main.js' ),
		true
	);

	if ( progress_bar_is_allowed() ) {
		wp_enqueue_style( 'wp-post-reading-progress' );
		if ( enable_progress_bar_html() ) {
			wp_enqueue_script( 'wp-post-reading-progress' );
		}
	}
}

/**
 * Enqueue the scripts and styles for the front-end.
 *
 * @param string $admin_page The admin settings page slug.
 */
function enque_admin_scripts( string $admin_page ) {
	global $plugin_admin_page;

	wp_register_style(
		'admin-wp-post-reading-progress',
		plugin_dir_url( __FILE__ ) . 'build/admin-style.css',
		[],
		filemtime( plugin_dir_path( __FILE__ ) . 'build/admin-style.css' )
	);

	if ( $plugin_admin_page === $admin_page ) {
		wp_enqueue_style( 'admin-wp-post-reading-progress' );
	}
}

/**
 * Get's the HTML for the progress bar.
 *
 * @return string The HTML string for the progress bar.
 */
function get_progress_bar() : string {
	ob_start();
	require_once 'template/reading-progress-bar.php';

	return ob_get_clean();
}

/**
 * Add the progress bar to the page.
 *
 * @param string $content The post/page content.
 * @return string The content, prepended with the progress HTML.
 */
function maybe_show_content_wp_reading_progress( string $content ) : string {
	$options   = get_wp_reading_progress_options();
	$post_type = get_post_type( get_the_ID() );

	if ( in_array( $options['post_type'], [ 'wp_core_all', $post_type ], true ) && progress_bar_is_allowed() ) {
		return get_progress_bar() . $content;
	}

	return $content;
}

/**
 * Remove the saved option once the plugin uninstalls.
 */
function remove_the_database_option() {
	$options = get_wp_reading_progress_options();

	if ( isset( $options['delete_db_settings'] ) && 'yes' === $options['delete_db_settings'] ) {
		delete_option( 'wp-post-reading-progress' );
	}
}
