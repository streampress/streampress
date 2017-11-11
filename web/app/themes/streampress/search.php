<?php
/**
 * The template for displaying search results pages.
 *
 * @package understrap
 */

get_header();

$args = array(
	'post_type' => 'sp_video_post',
	's' => sanitize_text_field( $_GET['s'] )
);

$query = new WP_Query( $args );
?>

<div class="wrapper page" id="search-wrapper">

	<div class="container-fluid" id="content" tabindex="-1">

		<div class="row">

			<div class="col-12 content-area" id="primary">

				<main class="site-main" id="main">

					<?php if ( $query->have_posts() ) : ?>

						<header class="page-header">

								<h1 class="page-title"><?php printf(
								 esc_html__( '%s results for %s', 'streampress' ),
									$query->post_count, "<i>".get_search_query()."</i>" ); ?></h1>

						</header><!-- .page-header -->

						<!-- Remaining videos -->
						<?php while( $query->have_posts() ): ?>
							<?php $query->the_post(); ?>

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

		</div><!-- .row -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
