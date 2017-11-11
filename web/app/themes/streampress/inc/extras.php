<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package understrap
 */

if ( ! function_exists( 'understrap_body_classes' ) ) {
	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @param array $classes Classes for the body element.
	 *
	 * @return array
	 */
	function understrap_body_classes( $classes ) {
		// Adds a class of group-blog to blogs with more than 1 published author.
		if ( is_multi_author() ) {
			$classes[] = 'group-blog';
		}
		// Adds a class of hfeed to non-singular pages.
		if ( ! is_singular() ) {
			$classes[] = 'hfeed';
		}

		return $classes;
	}
}
add_filter( 'body_class', 'understrap_body_classes' );

// Removes tag class from the body_class array to avoid Bootstrap markup styling issues.
add_filter( 'body_class', 'adjust_body_class' );

if ( ! function_exists( 'adjust_body_class' ) ) {
	/**
	 * Setup body classes.
	 *
	 * @param string $classes CSS classes.
	 *
	 * @return mixed
	 */
	function adjust_body_class( $classes ) {

		foreach ( $classes as $key => $value ) {
			if ( 'tag' == $value ) {
				unset( $classes[ $key ] );
			}
		}

		return $classes;

	}
}

// Filter custom logo with correct classes.
add_filter( 'get_custom_logo', 'change_logo_class' );

if ( ! function_exists( 'change_logo_class' ) ) {
	/**
	 * Replaces logo CSS class.
	 *
	 * @param string $html Markup.
	 *
	 * @return mixed
	 */
	function change_logo_class( $html ) {

		$html = str_replace( 'class="custom-logo"', 'class="img-fluid"', $html );
		$html = str_replace( 'class="custom-logo-link"', 'class="navbar-brand custom-logo-link"', $html );
		$html = str_replace( 'alt=""', 'title="Home" alt="logo"' , $html );

		return $html;
	}
}

/**
 * Display navigation to next/previous post when applicable.
 */
if ( ! function_exists( 'understrap_post_nav' ) ) :

	function understrap_post_nav() {
		// Don't print empty markup if there's nowhere to navigate.
		$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
		$next     = get_adjacent_post( false, '', false );

		if ( ! $next && ! $previous ) {
			return;
		}
		?>
				<nav class="container navigation post-navigation">
					<h2 class="sr-only"><?php _e( 'Post navigation', 'streampress' ); ?></h2>
					<div class="row nav-links justify-content-between">
						<?php

							if ( get_previous_post_link() ) {
								previous_post_link( '<span class="nav-previous">%link</span>', _x( '<i class="fa fa-angle-left"></i>&nbsp;%title', 'Previous post link', 'streampress' ) );
							}
							if ( get_next_post_link() ) {
								next_post_link( '<span class="nav-next">%link</span>',     _x( '%title&nbsp;<i class="fa fa-angle-right"></i>', 'Next post link', 'streampress' ) );
							}
						?>
					</div><!-- .nav-links -->
				</nav><!-- .navigation -->

		<?php
	}
endif;


/**
 * Modify Buddypress button UI
 */
if ( ! function_exists( 'sp_buddypress_get_button' ) ) :

	function sp_buddypress_get_button( $content, $args, $misc ) {

		$args['link_class'] = 'btn btn-primary btn-subscribe ' . $args['link_class'];

		$button = new BP_Button( $args );

		return $button->contents;
	}
endif;

add_filter( 'bp_get_button', 'sp_buddypress_get_button', 10, 3 );


/**
 * Modify Buddypress get friend button
 */

if ( ! function_exists( 'sp_buddypress_get_friend_button' ) ) :

	function sp_buddypress_get_friend_button( $args ) {

		switch( $args["id"] ) {
			case "not_friends":
				$args['link_text'] = __( 'Subscribe', 'streampress' );
				break;
			case "pending":
				$args['link_text'] = __( 'Cancel subscribe request', 'streampress' );
				break;
			case "awaiting_response":
				$args['link_text'] = __( 'Awaiting response', 'streampress' );
				break;
			case "is_friend":
				$args['link_text'] = __( 'Unsubscribe', 'streampress' );
				break;
			default:
				$args['link_text'] = __( 'Default', 'streampress' );
		}

		return $args;
	}
endif;

add_filter( 'bp_get_add_friend_button', 'sp_buddypress_get_friend_button', 15, 1 );

/**
 * Display subscribe button for logged out users
 * Clicking "subscribe" will redirect to login page
 */
if ( ! function_exists( 'sp_loggedout_friend_button' )) {

	function sp_loggedout_friend_button() {

		$args = array(
			'id'                => 'not_friends',
			'component'         => 'friends',
			'must_be_logged_in' => false,
			'block_self'        => true,
			'wrapper_class'     => 'friendship-button',
			'wrapper_id'        => 'friendship-button',
			'link_href'         => wp_login_url( get_permalink() ),
			'link_text'         => __( 'Subscribe', 'buddypress' ),
			'link_id'           => 'friend',
			'link_rel'          => 'add',
			'link_class'        => 'friendship-button not_friends add'
		);

		$button = new BP_Button( $args );

		echo apply_filters( 'bp_get_button', $button->contents, $args, $button );
	}
}
