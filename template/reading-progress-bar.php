<?php
/**
 * Template for creating the front-end of the progress bar.
 *
 * @package wp-post-reading-progress
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

$options        = WP_POST_READING_PROGRESS\HELPERS\get_wp_reading_progress_options();
$location_class = ( empty( $options['location'] ) ) ? 'nolocation' : $options['location'];
?>
<?php if ( ! isset( $options['time_to_read_only'] ) || 'yes' !== $options['time_to_read_only'] ) : ?>

<style>
	.kst.wp-reading-progress-bar {
		background-color:<?php echo esc_attr( $options['box_color'] ); ?>;
		color:<?php echo esc_attr( $options['box_text_color'] ); ?>
	}
	.kst.wp-reading-progress-bar progress {
		background-color:<?php echo esc_attr( $options['bar_color'] ); ?>
	}
	.kst.wp-reading-progress-bar progress::-webkit-progress-bar {
		background-color:<?php echo esc_attr( $options['bar_color'] ); ?>
	}
	.kst.wp-reading-progress-bar progress::-webkit-progress-value {
		background-color:<?php echo esc_attr( $options['value_color'] ); ?>
	}
	.kst.wp-reading-progress-bar progress::-moz-progress-bar {
		background-color:<?php echo esc_attr( $options['value_color'] ); ?>
	}

	<?php echo esc_html( apply_filters( 'wp_post_reading_progress_style', '' ) ); ?>
</style>

<?php endif; ?>

<div class="kst wp-reading-progress-bar <?php echo esc_attr( $location_class ); ?>">
	<?php if ( ! isset( $options['time_to_read_only'] ) || 'yes' !== $options['time_to_read_only'] ) : ?>

	<label for="kst-wp-reading-progress-bar">
		<span><?php esc_html_e( 'Reading Progress:', 'wp-post-reading-progress' ); ?></span>
		<progress id="kst-wp-reading-progress-bar" value="0" max="100"></progress>
		<div></div>
	</label>

	<?php endif; ?>

	<?php
	if ( ( isset( $options['time_to_read'] ) && 'yes' === $options['time_to_read'] ) ||
			( isset( $options['time_to_read_only'] ) && 'yes' === $options['time_to_read_only'] ) ) :
		?>

	<p class="reading-time">
		<?php
			printf(
				'%1$s<span>%2$s</span>',
				esc_html__( 'Estimated reading time:', 'wp-post-reading-progress' ),
				esc_html( WP_POST_READING_PROGRESS\HELPERS\calculate_reading_time() )
			);
		?>
	</p>

	<?php endif; ?>
</div>
