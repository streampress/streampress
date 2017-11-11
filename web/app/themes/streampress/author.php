<?php
/**
 * The template for displaying the author pages.
 *
 * Learn more: https://codex.wordpress.org/Author_Templates
 *
 * @package understrap
 */

$author = get_user_by( 'slug', get_query_var( 'author_name' ) );

$args = array(
	'post_type' => 'sp_video_post',
	'posts_per_page' => 10,
	'author' => $author->ID
);

$videos = new WP_Query( $args );

get_header();
?>

<div class="wrapper page" id="author-wrapper">

	<div class="container-fluid" id="content" tabindex="-1">

		<div class="row">

			<div class="col-12 col-sm-8 offset-sm-2 content-area" id="primary">

			<main class="site-main" id="main">

				<header class="page-header author-header">

					<div class="entry-meta">
						<div class="avatar-container">
							<a href="<?php echo get_author_posts_url( $author->ID ); ?>">
								<?php echo get_avatar( $author->ID, 120 ); ?>
							</a>
						</div>

						<div class="byline-container">
							<h3><?php echo $author->display_name; ?></h3>
						</div>

						<div class="subscribe-container">
							<div class="btn btn-primary subscribe-btn">Subscribe</div>
						</div>

					</div>

					<h5><?php esc_html_e( 'Posts by', 'streampress' ); ?> <?php echo esc_html( get_query_var( 'author_name' ) ); ?>
						:</h5>

				</header><!-- .page-header -->

				<!-- The Loop -->
				<?php if ( $videos->have_posts() ) : ?>
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
				<!-- End Loop -->

			</main><!-- #main -->

			<!-- The pagination component -->
			<?php understrap_pagination(); ?>

		</div><!-- #primary -->

	</div> <!-- .row -->

</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
