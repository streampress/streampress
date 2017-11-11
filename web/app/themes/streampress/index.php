<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package understrap
 */

// Display trending videos
$trending = new WP_Query( array(
	'post_type' => 'sp_video_post',
	'posts_per_page' => 8,
));

// Display videos from categories that have three of more videos
$categories = array();

$active_categories = get_terms( 'sp_video_category', array(
    'hide_empty' => true,
));

// Categories with more than 4 videos will be displayed on frontpage
foreach ( $active_categories as $category ) {
	if ( $category->count > 4 ) {
		array_push( $categories, $category );
	}
}

get_header();
?>

<?php if ( is_front_page() && is_home() ) : ?>
	<?php get_template_part( 'global-templates/hero', 'none' ); ?>
<?php endif; ?>

<div class="wrapper page" id="wrapper-index">

	<div class="container-fluid" id="content" tabindex="-1">

		<div class="row">
			<div class="col-12 content-area" id="primary">

				<main class="site-main" id="main">

					<?php if ( $trending->have_posts() ) : ?>

						<!-- Trending -->
						<div class="row">
							<div class="col-12">
								<h3><?php echo __('Trending', 'streampress'); ?></h3>
							</div>
						</div>

						<div class="row">

							<div class="col-12">
								<div class="row">
									<div class="slider-container slider-fullwidth">

										<div class="slider-items slider">

										<?php while( $trending->have_posts() ): ?>
											<?php $trending->the_post(); ?>

											<div class="slider-item">
												<a href="<?php echo get_post_permalink(); ?>" class="thumbnail thumbnail-vertical">
													<div class="image-container" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_thumbnail', true) ); ?>); ">
														<div class="image" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_gif', true) ); ?>); ">
														</div>
														<div class="play icon fa fa-play"></div>
														<div class="duration badge badge-default">
															<?php echo esc_html( get_post_meta( get_the_ID(), 'sp_video_duration', true) ); ?>
														</div>
													</div>

													<div class="text-container">
														<h5 class="title"><?php the_title(); ?></h5>
													</div>
												</a>
											</div>
										<?php endwhile; ?>

										</div>
									</div>
								</div>
							</div>
						</div>

					<?php endif; ?>

					<?php if ( sizeof( $categories ) > 1 ) : ?>

						<!-- Categories -->
						<div class="row">
							<div class="col-12">
								<hr>
							</div>
						</div>

						<div class="row">

							<?php foreach( $categories as $index=>$category ): ?>

								<!-- Category -->
								<div class="col-12 col-md-6">

									<div class="row">
										<div class="col-12">
											<h3><?php echo $category->name; ?></h3>
										</div>
									</div>

									<?php
										$videos = new WP_Query( array(
											'post_type' => 'sp_video_post',
											'posts_per_page' => 8,
											'tax_query' => array(
												array(
													'taxonomy' => 'sp_video_category',
													'field'    => 'slug',
													'terms'    => $category->slug
												),
											),
										));
									?>

									<div class="row">
										<div class="slider-container slider-halfwidth slider-<?php if( $index % 2 === 0 ): ?>left<?php else: ?>right<?php endif; ?>">

											<div class="slider-items slider">

												<?php while( $videos->have_posts() ): ?>

													<?php $videos->the_post(); ?>

													<div class="slider-item">
														<a href="<?php the_permalink(); ?>" class="thumbnail thumbnail-vertical">
															<div class="image-container" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_thumbnail', true) ); ?>); ">
																<div class="image" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_gif', true) ); ?>); ">
																</div>
																<div class="play icon fa fa-play"></div>
																<div class="duration badge badge-default">
																	<?php echo esc_html( get_post_meta( get_the_ID(), 'sp_video_duration', true) ); ?>
																</div>
															</div>

															<div class="text-container">
																<h5 class="title"><?php the_title(); ?></h5>
															</div>
														</a>
													</div>

												<?php endwhile; ?>

											</div>

										</div>
									</div>

								</div>

							<?php endforeach; ?>

						</div>

					<?php endif; ?>

				</main><!-- #main -->

			</div><!-- #primary -->

		</div><!-- .row -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
