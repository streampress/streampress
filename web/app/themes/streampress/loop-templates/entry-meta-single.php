<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */
?>

<div class="entry-meta">

	<div class="avatar-container">
		<a href="<?php echo home_url( 'members' ); ?>/<?php echo get_the_author_meta( 'user_nicename' ); ?>">
			<?php echo get_avatar( get_the_author_meta( 'ID' ), 120 ); ?>
		</a>
	</div>

	<div class="byline-container">
		<div class="author"><?php the_author(); ?></div>
		<time class="date"><?php echo esc_html( get_the_date( 'M j, Y' ) ); ?></time>
	</div>

	<div class="subscribe-container">

		<?php if ( is_user_logged_in() ) { ?>
			<?php bp_add_friend_button( get_the_author_meta( 'ID' ) ) ?>
		<?php } else { ?>
			<?php sp_loggedout_friend_button(); ?>
		<?php } ?>

	</div>

</div>