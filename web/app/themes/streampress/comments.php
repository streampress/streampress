<?php
/**
 * The template for displaying comments.
 *
 * The area of the page that contains both current comments
 * and the comment form.
 *
 * @package understrap
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<div class="comments-area" id="comments">

	<?php if ( have_comments() ) : ?>

		<ol class="comment-list">

			<?php
			wp_list_comments( array(
				'style'      => 'ol',
				'short_ping' => true,
			) );
			?>

		</ol><!-- .comment-list -->

	<?php endif; ?>

	<?php comment_form(); // Render comments form. ?>

</div><!-- #comments -->
