<?php
/**
 * The right sidebar containing the main widget area.
 *
 * @package understrap
 */

$author_id = false;

if ( is_single() ) {
	wp_reset_query();

	while( have_posts() ) {
		the_post();
		$author_id = get_the_author_meta('ID');
	}
}

$args = array(
	'post_type' => 'sp_video_post',
	'posts_per_page' => 15,
);

if( is_home() ) {
	$args['posts_per_page'] = 5;
	$args['offset'] = 5;
}

$videos = new WP_Query( $args );
?>

<div class="col-12 col-lg-4 widget-area sidebar" id="right-sidebar" role="complementary">

	<div class="sidebar-container">

		<?php if( is_single() ): ?>

			<!-- Controls -->
			<div class="row sidebar-controls">
				<div class="col-12">
					<div class="text float-left">
						<?php echo esc_html__('Up Next', 'streampress');  ?>
					</div>

					<div class="text">
						<?php echo esc_html__('Autoplay', 'streampress');  ?>
					</div>

					<div class="btn-group autoplay" data-toggle="buttons">
						<label class="btn btn-primary active">
							<input type="radio" id="autoplay-on" checked autocomplete="off">On
						</label>
						<label class="btn btn-primary">
							<input type="radio" id="autoplay-off" autocomplete="off">Off
						</label>
					</div>

				</div>
			</div>

		<?php endif; ?>

		<?php if( is_single() ): ?>

			<!-- Next video -->
			<?php if( $videos->have_posts() ): ?>
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

			<?php endif; ?>
		<?php endif; ?>

		<div class="row">
			<div class="col-12">
				<hr>
			</div>
		</div>

		<?php if( is_single() && $author_id ): ?>

			<!-- Playlists -->
			<?php
				$args = array(
				    'taxonomy' => 'sp_playlist',
				    'hide_empty' => true,
					'orderby'  => 'count',
					'order'    => 'DESC',
					'meta_key' => 'user_id',
					'meta_value' => $author_id
				);

				$terms = get_terms( $args );
			?>

			<?php foreach ( $terms as $term ): ?>

				<?php

					$args = array(
						'tax_query' => array(
							array(
								'taxonomy' => 'sp_playlist',
								'field'    => 'slug',
								'terms'    => $term->slug
							)
						)
					);

					$playlist = new WP_Query( $args );

					if ( $playlist->have_posts() ) {
						$playlist->the_post();
						?>

						<a href="<?php echo get_term_link( $term->term_id ); ?>" class="thumbnail playlist">
							<div class="image-container" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_thumbnail', true) ); ?>); ">
								<div class="image" style="background-image: url(<?php echo esc_attr( get_post_meta( get_the_ID(), 'sp_video_gif', true) ); ?>); ">
								</div>
								<div class="overlay text-center">
									<div class="info">
										<div class="count"><?php echo $playlist->post_count; ?></div>
										<div class="text"><?php echo esc_html__('View all', 'streampress'); ?></div>
										<i class="icon fa fa-th-list"></i>
									</div>
								</div>
								<div class="play icon fa fa-play"></div>
							</div>

							<div class="text-container">
								<h4 class="title"><?php echo $term->name; ?></h4>
							</div>
						</a>

						<?php
						wp_reset_postdata();
					}

				?>

			<?php endforeach; ?>

		<?php endif; ?>

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

	</div>

	<div class="row">
		<div class="col-12">

			<?php dynamic_sidebar( 'right-sidebar' ); ?>

		</div>
	</div>

</div><!-- #secondary -->
