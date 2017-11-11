
	<!-- Slideout menu -->
	<nav id="slideout" class="embedded-menu <?php if( is_user_logged_in() ): ?>logged-in<?php endif;?>">
		<header>

			<?php
				$slideout = wp_nav_menu( array(
					'theme_location' => 'slideout',
					'echo' => false
				));

				if ( empty( $slideout ) ) {
					streampress_slideout_menu();
				} else {
					echo $slideout;
				}
			?>

			<hr>

			<ul class="category-sidemenu">
				<?php
					$terms = get_terms( array(
					    'taxonomy' => 'sp_video_category',
					    'orderby' => 'name',
					    'order' => 'ASC',
					    'hide_empty' => false,
					));
				?>

				<?php foreach( $terms as $term ): ?>
					<li><a href="<?php echo get_category_link( $term->term_id ); ?>"><?php echo $term->name; ?></a></li>
				<?php endforeach; ?>

			</ul>

		</header>
	</nav>
