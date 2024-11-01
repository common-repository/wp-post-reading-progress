<?php
/**
 * Helpers functions for adding the plugin functionality.
 *
 * @package wp-post-reading-progress
 */

declare(strict_types=1);

namespace WP_POST_READING_PROGRESS\HELPERS;

defined( 'ABSPATH' ) || exit;

/**
 * Calculates the reading time of an article or page, based on the content size.
 *
 * @param string|null $article_id Optional. The article id for which we calculate the readint time.
 * @return string The reading time for the article.
 */
function calculate_reading_time( ?string $article_id = null ) : string {
	if ( empty( $article_id ) ) {
		$article_id = get_the_ID();
	}
	$base_count    = intval( get_wp_reading_progress_options()['time_to_read_base'] );
	$content_count = intval( str_word_count( wp_strip_all_tags( excerpt_remove_blocks( get_the_content( $article_id ) ) ) ) );
	$reading_time  = $content_count * 60 / $base_count;

	/* translators: %1$02d: number of minutes, with leading zero, %2$02d: number of seconds, with leading zero */
	$time_format_string = esc_html__( '%1$02d minutes and  %2$02d seconds', 'wp-post-reading-progress' );

	if ( $reading_time < 60 ) {
		return sprintf(
			'%1$02d %2$s',
			intval( $reading_time ),
			esc_html__( 'seconds', 'wp-post-reading-progress' )
		);
	}

	return seconds_to_minutes( intval( $reading_time ), $time_format_string );
}

/**
 * Helper function to get the plugin options.
 *
 * @return null|array
 */
function get_wp_reading_progress_options() : ?array {

	if ( empty( get_option( 'wp-post-reading-progress' ) ) ) {
		return null;
	}

	return get_option( 'wp-post-reading-progress' );
}

/**
 * Converts the time in seconds to minutes.
 *
 * @param int    $time   Timestamp in seconds.
 * @param string $format The format we want.
 * @return string|null The time in minutes or null.
 */
function seconds_to_minutes( int $time, string $format = '%02d:%02d' ) : ?string {
	if ( 1 > $time ) {
		return null;
	}

	$minutes = floor( $time / 60 );
	$seconds = ( $time % 60 );

	return sprintf( $format, $minutes, $minutes );
}

/**
 * Checks if plugin is allowed on page or not.
 *
 * @return bool
 */
function progress_bar_is_allowed() : bool {
	$plugin_usage = get_wp_reading_progress_options()['post_type'];

	if ( ( is_single() && 'post' === $plugin_usage ) || ( is_page() && 'page' === $plugin_usage ) || ( ( is_single() || is_page() ) && 'wp_core_all' === $plugin_usage ) ) {
		return true;
	}
	return false;
}

/**
 * Helpre function to chekc wether the showing only time for reading option is set.
 * Used in wp-post-reading-progress.php, enque_front_scripts():148.
 *
 * @return bool
 */
function enable_progress_bar_html() : bool {
	$show_reading_time_only = get_wp_reading_progress_options()['time_to_read_only'];

	return ( isset( $show_reading_time_only ) && 'yes' === $show_reading_time_only ) ? false : true;
}
