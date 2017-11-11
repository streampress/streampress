<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package understrap
 */
$args = array(
	'post_type' => 'sp_video_post',
	'posts_per_page' => 10,
);

// Category archive
if( !empty( $wp_query->query_vars['category_name'] ) ) {
	$args['category_name'] = $wp_query->query_vars['category_name'];
}

// Playlist archive
if( !empty( $wp_query->query_vars['sp_playlist'] ) ) {
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'sp_playlist',
			'field'    => 'slug',
			'terms'    => $wp_query->query_vars['sp_playlist']
		)
	);
}

$videos = new WP_Query( $args );

get_header();
?>

<div class="wrapper page" id="archive-wrapper">

	<div class="container-fluid" id="content" tabindex="-1">

		<div class="row">

			<div class="col-12 col-sm-10 push-sm-1 col-lg-8 push-lg-2 content-area" id="primary">

				<main class="site-main" id="main">

					<?php if ( $videos->have_posts() ) : ?>

						<header class="page-header">
							<?php
							the_archive_title( '<h1 class="page-title">', '</h1>' );
							the_archive_description( '<div class="taxonomy-description">', '</div>' );
							?>
						</header><!-- .page-header -->

						<!-- Remaining videos -->
						<?php while( $videos->have_posts() ): ?>
							<?php $videos->the_post(); ?>

							<a href="<?php echo get_post_permalink(); ?>" class="thumbnail">
								<div class="image-container" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_thumbnail', true) ); ?>); ">
									<div class="image" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_gif', true) ); ?>); ">
									</div>
									<div class="play icon fa fa-play"></div>
									<div class="duration badge badge-default">
										<?php echo esc_html( get_post_meta( get_the_ID(), 'sp_video_duration', true) ); ?>
									</div>
								</div>

								<div class="text-container">
									<h4 class="title"><?php the_title(); ?></h4>
								</div>
							</a>

						<?php endwhile; ?>

					<?php else : ?>

						<?php get_template_part( 'loop-templates/content', 'none' ); ?>

					<?php endif; ?>

				</main><!-- #main -->

			</div><!-- #primary -->

		</div> <!-- .row -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
