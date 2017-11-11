<?php
/**
 * Streampress Playlist Archive
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package understrap
 */

$name = $wp_query->query_vars['sp_playlist'];

$args = array(
	'post_type' => 'sp_video_post',
	'posts_per_page' => 10,
);

$args['tax_query'] = array(
	array(
		'taxonomy' => 'sp_playlist',
		'field'    => 'slug',
		'terms'    => $name
	)
);

$videos = new WP_Query( $args );

get_header();

?>

<div class="wrapper page" id="archive-wrapper">

	<div class="container-fluid" id="content" tabindex="-1">

		<div class="row">

			<div class="col-12 content-area" id="primary">

				<main class="site-main" id="main">

					<?php if ( $videos->have_posts() ) : ?>

							<div class="col-12 playlist-container">

								<div class="row">

									<!-- First video in the playlist -->
									<div class="col-12 col-md-8">

										<?php if( $videos->have_posts() ): ?>
											<?php $videos->the_post(); ?>

											<div class="row">
												<div class="col-12 video-container">
													<?php
														// Video embed for a video post
														if ( function_exists( 'sp_video_post_embed' ) ) {
															sp_video_post_embed( $post->ID );
														}
													?>
												</div>
											</div>

											<?php $videos->rewind_posts(); ?>
										<?php endif; ?>
									</div>

									<!-- Playlist feed (tablet & up) -->
									<div class="col-12 col-sm-4 hidden-sm-down playlist-controls-container">

										<div class="row">
											<div class="col-12 playlist-title">
												<?php echo single_term_title('', false); ?>
											</div>
										</div>

										<div class="playlist-controls">

										<?php while( $videos->have_posts() ): ?>
											<?php $videos->the_post(); ?>

												<a href="#" class="thumbnail playlist-item" data-index="<?php echo $videos->current_post; ?>" data-slug="<?php echo $post->post_name; ?>">
													<div class="image-container" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_thumbnail', true) ); ?>); ">
														<div class="image" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_gif', true) ); ?>); ">
														</div>
														<div class="play icon fa fa-play"></div>
													</div>

													<div class="text-container">
														<h4 class="title"><?php the_title(); ?></h4>
														<?php echo sprintf( '<div class="playlist-name">%s</div>', get_the_author() ); ?>
														<div class="author"><?php ; ?></div>
													</div>
												</a>

										<?php endwhile; ?>

										</div>
									</div>

									<!-- Playlist feed (mobile) -->
									<div class="col-12 playlist-controls-container">

										<div class="playlist-controls">

											<div class="row">
												<div class="slider-container slider-playlist">

													<div class="slider-items slider">

													<?php while( $videos->have_posts() ): ?>
														<?php $videos->the_post(); ?>

														<div class="slider-item">
															<a href="<?php echo get_post_permalink(); ?>" class="thumbnail thumbnail-vertical playlist-item" data-index="<?php echo $videos->current_post; ?>" data-slug="<?php echo $post->post_name; ?>">
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
								</div>
							</div>


							<div class="col-12">
								<div class="row">
									<div class="col-12 col-lg-8 current-video-info">

										<?php if( $videos->have_posts() ): ?>
											<?php $videos->the_post(); ?>

											<div class="row">
												<div class="col-12 pt-2">

													<!-- Title -->
													<h1 id="form-title" class="title entry-title"><?php the_title(); ?></h1>

													<!-- Statistics -->
													<?php echo get_template_part( 'loop-templates/entry-statistics' ); ?>

													<hr>

													<!-- Meta / byline -->
													<?php echo get_template_part( 'loop-templates/entry-meta', 'single' ); ?>

													<!-- Description -->
													<?php if ( function_exists( 'sp_video_post_description' ) ): ?>
														<div class="video-description">
															<?php echo sp_video_post_description( get_the_ID() ); ?>
														</div>
													<?php endif; ?>

													<!-- Category -->
													<?php if ( function_exists( 'sp_video_post_category' ) ): ?>
														<div class="row">
															<div class="col-4 col-md-3 col-lg-2">
																<?php echo esc_html__( 'Category', 'streampress-video-post' ) ?>
															</div>
															<div class="col-8 col-md-9 col-lg-10 video-categories">
																<b><?php sp_video_post_category( $post->ID ); ?></b>
															</div>
														</div>
													<?php endif; ?>

													<!-- License -->
													<?php if ( function_exists( 'sp_video_post_license' ) ): ?>
													<div>
														<div class="row">
															<div class="col-4 col-md-3 col-lg-2">
																<?php echo esc_html__( 'License', 'streampress-video-post' ) ?>
															</div>
															<div class="col-8 col-md-9 col-lg-10 video-license">
																<?php sp_video_post_license( $post->ID ); ?>
															</div>
														</div>
													</div>
													<?php endif; ?>

													<!-- Comments -->
													<?php if ( comments_open() ): ?>
														<div class="pt-3">
															<?php comments_template(); ?>
														</div>
													<?php endif; ?>

												</div>
											</div>

											<?php $videos->rewind_posts(); ?>
										<?php endif; ?>

									</div>

									<?php get_sidebar( 'right' ); ?>
								</div>
							</div>

					<?php else : ?>

						<?php get_template_part( 'loop-templates/content', 'none' ); ?>

					<?php endif; ?>

				</main><!-- #main -->

			</div><!-- #primary -->

		</div> <!-- .row -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<!-- Playlist JSON -->
<?php $items = array(); ?>

<?php while( $videos->have_posts() ) {
	$videos->the_post();
	$item = array();

	$item['slug'] = $post->post_name;

	$item['poster'] = get_post_meta( get_the_ID(), 'sp_video_thumbnail', true );

	$source = array(
		'src' => get_post_meta( get_the_ID(), 'sp_video_filename', true ),
		'type' => get_post_meta( get_the_ID(), 'sp_video_type', true )
	);

	$item['sources'] = array();

	array_push( $item['sources'], $source );

	array_push( $items, $item );
}
wp_reset_postdata();


$playlist = array();
$playlist['name'] = $name;
$playlist['items'] = $items;
?>

<script>
	// Video playlist configuration
	SP.playlist = <?php echo json_encode( $playlist ); ?>;
	SP.current = "<?php echo empty($_GET['v']) ? $playlist['items'][0]['slug'] : strip_tags( $_GET['v']); ?>";
</script>

<?php get_footer(); ?>
