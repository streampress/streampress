<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */

?>
<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<header class="entry-header">
		<?php echo get_the_post_thumbnail( $post->ID, 'large' ); ?>
	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php
			if ( 'sp_video_post' == $post->post_type ) {

				// Video embed for a video post
				if ( function_exists( 'sp_video_post_embed' ) ) {
					sp_video_post_embed( $post->ID );
				}
			}
		?>

		<?php the_title( '<h3 class="entry-title">', '</h3>' ); ?>

		<!-- Statistics -->
		<?php echo get_template_part( 'loop-templates/entry-statistics' ); ?>

		<hr>

		<?php echo get_template_part( 'loop-templates/entry-meta', 'single' ); ?>

		<?php
			if ( 'sp_video_post' == $post->post_type ) {
				echo '<div class="video-description">';
					if ( function_exists( 'sp_video_post_description' ) ) {
						sp_video_post_description( $post->ID );
					}
				echo '</div>';
			}
		?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<?php
			if ( 'sp_video_post' == $post->post_type ) {

				echo '<div class="video-metadata">';

				if ( function_exists( 'sp_video_post_category' ) ) {
				?>
					<!-- Category -->
					<div class="row">
						<div class="col-4 col-md-3">
							<?php echo esc_html__( 'Category', 'streampress-video-post' ) ?>
						</div>
						<div class="col-8 col-md-9 video-categories">
							<?php sp_video_post_category( $post->ID ); ?>
						</div>
					</div>
				<?php
				}

				if ( function_exists( 'sp_video_post_license' ) ) {
				?>
					<!-- License -->
					<div class="row">
						<div class="col-4 col-md-3">
							<?php echo esc_html__( 'License', 'streampress-video-post' ) ?>
						</div>
						<div class="col-8 col-md-9 video-license">
							<?php sp_video_post_license( $post->ID ); ?>
						</div>
					</div>
				<?php
				}

				echo '</div>';
			}
		?>

		<hr>

	</footer><!-- .entry-footer -->

</article><!-- #post-## -->
