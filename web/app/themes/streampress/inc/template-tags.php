<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package understrap
 */

if ( ! function_exists( 'streampress_slideout_menu' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function streampress_slideout_menu() {
?>
			<ul>
				<li>
					<a href="<?php home_url('/'); ?>"><?php echo esc_html__( 'Homepage', 'streampress' )?></a>
				</li>

				<?php if( is_user_logged_in() ): ?>
				<?php $current_user = wp_get_current_user(); ?>
				<!-- Logged in user -->
				<li>
					<a href="<?php echo esc_url( home_url('/members/' . $current_user->user_nicename) ); ?>"><?php echo esc_html__( 'Profile', 'streampress' )?></a>
				</li>

				<?php else: ?>
				<!-- Logged out / unregistered out user -->
				<li>
					<a href="<?php echo wp_login_url( home_url('/') ); ?>"><?php echo esc_html__( 'Login', 'streampress' )?></a>
				</li>

				<li>
					<a href="<?php echo esc_url( home_url('/register') ); ?>"><?php echo esc_html__( 'Register', 'streampress' )?>
				</li>
				<?php endif; ?>

				<li>
					<a href="<?php echo esc_url( home_url('/activity') ); ?>"><?php echo esc_html__( 'Activity', 'streampress' )?></a>
				</li>
				<li>
					<a href="<?php echo esc_url( home_url('/members') ); ?>"><?php echo esc_html__( 'Members', 'streampress' )?></a>
				</li>

				<?php if( is_user_logged_in() ): ?>
				<li>
					<a href="<?php echo wp_logout_url( home_url('/') ); ?>"><?php echo esc_html__( 'Logout', 'streampress' )?></a>
				</li>
				<?php endif; ?>
			</ul>
<?php
}
endif;

if ( ! function_exists( 'understrap_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function understrap_posted_on() {
	$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
	}
	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);
	$posted_on = sprintf(
		esc_html_x( 'Posted on %s', 'post date', 'streampress' ),
		'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
	);
	$byline = sprintf(
		esc_html_x( 'by %s', 'post author', 'streampress' ),
		'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
	);
	echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
}
endif;

if ( ! function_exists( 'understrap_entry_footer' ) ) :
/**
 * Prints HTML with meta information for the categories, tags and comments.
 */
function understrap_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'streampress' ) );
		if ( $categories_list && understrap_categorized_blog() ) {
			printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', 'streampress' ) . '</span>', $categories_list ); // WPCS: XSS OK.
		}
		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html__( ', ', 'streampress' ) );
		if ( $tags_list ) {
			printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', 'streampress' ) . '</span>', $tags_list ); // WPCS: XSS OK.
		}
	}
	if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
		echo '<span class="comments-link">';
		comments_popup_link( esc_html__( 'Leave a comment', 'streampress' ), esc_html__( '1 Comment', 'streampress' ), esc_html__( '% Comments', 'streampress' ) );
		echo '</span>';
	}
	edit_post_link(
		sprintf(
			/* translators: %s: Name of current post */
			esc_html__( 'Edit %s', 'streampress' ),
			the_title( '<span class="screen-reader-text">"', '"</span>', false )
		),
		'<span class="edit-link">',
		'</span>'
	);
}
endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function understrap_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'understrap_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );
		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );
		set_transient( 'understrap_categories', $all_the_cool_cats );
	}
	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so components_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so components_categorized_blog should return false.
		return false;
	}
}

/**
 * Flush out the transients used in understrap_categorized_blog.
 */
function understrap_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	// Like, beat it. Dig?
	delete_transient( 'understrap_categories' );
}
add_action( 'edit_category', 'understrap_category_transient_flusher' );
add_action( 'save_post',     'understrap_category_transient_flusher' );

