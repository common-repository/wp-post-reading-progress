<?php
/**
 * Creates the admin settings menu and page.
 *
 * @package WP Post Reading Progress
 */

declare( strict_types=1 );

namespace WP_POST_READING_PROGRESS\Options;

defined( 'ABSPATH' ) || exit;

// Actions and filters.
add_action( 'admin_menu', __NAMESPACE__ . '\\admin_menu' );
add_action( 'admin_init', __NAMESPACE__ . '\\initiate_settings' );

/**
 * Creates the menu link for the plugin options page.
 * Also renders the options page, through the callback function.
 */
function admin_menu() {
	global $plugin_admin_page;
	$plugin_admin_page = add_submenu_page(
		'options-general.php',
		esc_html__( 'WP Post Reading Progress', 'wp-post-reading-progress' ),
		esc_html__( 'WP Post Reading Progress', 'wp-post-reading-progress' ),
		'manage_options',
		'wp_post_reading_progress',
		__NAMESPACE__ . '\\plugin_page_render'
	);
}

/**
 * Callback function to render the options page.
 */
function plugin_page_render() {
	global $kst_wp_progress_allowed_html;

	echo wp_kses(
		sprintf(
			'<form action="options.php" method="post"><h2>%1$S</h2>',
			esc_html__( 'WP Post Reading Progress', 'wp-post-reading-progress' )
		),
		$kst_wp_progress_allowed_html
	);

	settings_fields( 'wp-post-reading-progress-group' );

	do_settings_sections( 'wp_post_reading_progress' );

	submit_button( esc_html__( 'Save settings', 'wp-post-reading-progress' ) );

	echo wp_kses( '</form>', $kst_wp_progress_allowed_html );
}

/**
 * Creates the plugin options.
 */
function initiate_settings() {

	register_setting(
		'wp-post-reading-progress-group',
		'wp-post-reading-progress'
	);

	add_settings_section(
		'wp-post-reading-progress-section',
		esc_html__( 'WP Post Reading Progress Settings', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\wp_post_reading_progress_section',
		'wp_post_reading_progress'
	);

	add_settings_field(
		'post_type',
		esc_html__( 'Post type:', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'type'        => 'select',
			'id'          => 'post_type',
			'description' => esc_html__( 'The type of post type were the reading progress bar will be visible. Not choosing an option equals to not showing the content this plugin creates.', 'wp-post-reading-progress' ),
			'options'     => [
				'post'        => esc_html__( 'For posts only', 'wp-post-reading-progress' ),
				'page'        => esc_html__( 'For pages only', 'wp-post-reading-progress' ),
				'wp_core_all' => esc_html__( 'For pages and posts', 'wp-post-reading-progress' ),
			],
		]
	);

	add_settings_field(
		'location',
		esc_html__( 'Position', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'type'        => 'select',
			'id'          => 'location',
			'description' => esc_html__( 'The location of the reading progress bar, in the page.
			Not choosing an option will make the plugin to show on top of the content of the page or post.
			It will also float the progress bar in the right of the content.', 'wp-post-reading-progress' ),
			'options'     => [
				'top'    => esc_html__( 'Top', 'wp-post-reading-progress' ),
				'right'  => esc_html__( 'Right', 'wp-post-reading-progress' ),
				'bottom' => esc_html__( 'Bottom', 'wp-post-reading-progress' ),
				'left'   => esc_html__( 'Left', 'wp-post-reading-progress' ),
			],
		]
	);

	add_settings_field(
		'time_to_read_only',
		esc_html__( 'Show only the time to read', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'id'          => 'time_to_read_only',
			'description' => esc_html__( 'Show the calculated total time to read and hide the progress bar. This also hides the floating box, while srcolling the post content.', 'wp-post-reading-progress' ),
			'type'        => 'checkbox',
		]
	);

	add_settings_field(
		'time_to_read',
		esc_html__( 'Show time to read', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'id'          => 'time_to_read',
			'description' => esc_html__( 'Show the calculated total time to read, in the post meta.', 'wp-post-reading-progress' ),
			'type'        => 'checkbox',
		]
	);

	add_settings_field(
		'time_to_read_base',
		esc_html__( 'Time to read base', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'id'          => 'time_to_read_base',
			'description' => sprintf(
				'%1$s <a href="%2$s" tile="%3$s">%4$s</a></small>',
				esc_html__( 'The number of words used a base of calculation for the time to read. Default is 180 wpm.', 'wp-post-reading-progress' ),
				esc_url( 'https://en.wikipedia.org/wiki/Words_per_minute#Reading_and_comprehension' ),
				esc_html__( 'Wikipedia page section link', 'wp-post-reading-progress' ),
				esc_html__( 'Follow the link to find out more.', 'wp-post-reading-progress' )
			),
			'default'     => '180',
			'type'        => 'number',
		]
	);

	add_settings_field(
		'value_color',
		esc_html__( 'Progress Bar Completion Color', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'id'          => 'value_color',
			'description' => esc_html__( 'The progress bar color for the completed part.', 'wp-post-reading-progress' ),
			'type'        => 'color',
		]
	);

	add_settings_field(
		'bar_color',
		esc_html__( 'Progress Bar Empty Color', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'id'          => 'bar_color',
			'description' => esc_html__( 'The progress bar color for the remaining space.', 'wp-post-reading-progress' ),
			'default'     => '#ffffff',
			'type'        => 'color',
		]
	);

	add_settings_field(
		'box_color',
		esc_html__( 'Color for the progress box background.', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'id'          => 'box_color',
			'description' => esc_html__( 'The progress bar color for the remaining space.', 'wp-post-reading-progress' ),
			'default'     => '#000000',
			'type'        => 'color',
		]
	);

	add_settings_field(
		'box_text_color',
		esc_html__( 'Color for the progress box background.', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'id'          => 'box_text_color',
			'description' => esc_html__( 'The progress bar color for the text in it.', 'wp-post-reading-progress' ),
			'default'     => '#ffffff',
			'type'        => 'color',
		]
	);

	add_settings_field(
		'delete_db_settings',
		esc_html__( 'Delete the settings when plugin is disabled.', 'wp-post-reading-progress' ),
		__NAMESPACE__ . '\\create_field',
		'wp_post_reading_progress',
		'wp-post-reading-progress-section',
		[
			'id'          => 'delete_db_settings',
			'description' => esc_html__( 'The progress bar color for the text in it.', 'wp-post-reading-progress' ),
			'type'        => 'checkbox',
		]
	);
}

/**
 * Genrates the fields HTML.
 *
 * @param array $args The arguments used for creatng specific fields.
 */
function create_field( array $args ) : void {
	global $kst_wp_progress_allowed_html;

	$field = '';

	if ( ! empty( $args ) && isset( $args['type'] ) && isset( $args['id'] ) ) {
		// Get the saved settings data.
		$wp_pr_progress = get_option( 'wp-post-reading-progress' );
		$default_value  = ( isset( $args['default'] ) ) ? $args['default'] : '';
		$saved_value    = ( isset( $wp_pr_progress[ $args['id'] ] ) ) ? $wp_pr_progress[ $args['id'] ] : $default_value;

		switch ( $args['type'] ) {
			case 'select':
				$field  = '<select name="wp-post-reading-progress[' . esc_attr( $args['id'] ) . ']">';
				$field .= '<option value="">' . esc_html__( 'Choose ', 'wp-post-reading-progress' ) . ucfirst( str_replace( '_', ' ', $args['id'] ) ) . '</option>';
				foreach ( $args['options'] as $value => $name ) {
					$selected = selected( $saved_value, $value, false );
					$field   .= '<option ' . $selected . ' value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
				}
				$field .= '</select>';
				break;
			case 'checkbox':
				$field = sprintf(
					'<input type="checkbox" name="wp-post-reading-progress[%1$s]" value="yes" %2$s/>',
					esc_attr( $args['id'] ),
					checked( $saved_value, 'yes', false )
				);
				break;
			case 'number':
				$field = sprintf(
					'<input type="number" name="wp-post-reading-progress[%1$s]" min="0" max="900" step="5" value="%2$s" />',
					esc_attr( $args['id'] ),
					esc_attr( $saved_value )
				);
				break;
			case 'color':
				$field = sprintf(
					'<input type="color" name="wp-post-reading-progress[%1$s]" value="%2$s" />',
					esc_attr( $args['id'] ),
					esc_attr( $saved_value )
				);
				break;
			default:
				$field = sprintf(
					'<input type="text" name="%1$s" value="%2$s" />',
					esc_attr( $args['id'] ),
					esc_attr( $saved_value )
				);
		}
		if ( isset( $args['description'] ) ) {
			$field .= sprintf(
				'<p><small>%s</small></p>',
				wp_kses(
					$args['description'], [
						'em' => [],
						'a'  => [
							'href'  => [],
							'title' => [],
						],
					]
				)
			);
		}
	}
	echo wp_kses( $field, $kst_wp_progress_allowed_html );
}

/**
 * Renders the page section.
 */
function wp_post_reading_progress_section() {
	global $kst_wp_progress_allowed_html;
	wp_kses(
		printf(
			'<p>%s</p>',
			esc_html__( 'Customise your website posts/articles reading progress bar.', 'wp-post-reading-progress' )
		),
		$kst_wp_progress_allowed_html
	);
}
