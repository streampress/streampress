<?php
/*
Plugin Name: Streampress Video Post
Plugin URI: http://streampress.io
Description: Adds a video post type, a video playlist custom taxonomy, an RSS feed and provides a shortcode for embedded video players.
Version: 1.0
Author: ClickOn
Author URI: http://www.clickon.co/tech
Text Domain: streampress-video-post
Domain Path: /languages
*/

// Access only via Wordpress
defined( 'ABSPATH' ) or die();

// Paths
define( 'SP_VIDEO_POST_PATH',       	plugin_dir_path( __FILE__ ) );
define( 'SP_VIDEO_POST_URL',        	plugin_dir_url( __FILE__ ));

// Basic configuration
define( 'SP_VIDEO_POST_VERSION',    	'1.0' );
define( 'SP_CDN_URL',        			'https://bitdash-a.akamaihd.net/content/MI201109210084_1/m3u8s/' );

// Licenses
define ( 'SP_LICENSE_URL', 'https://en.wikipedia.org/wiki/Creative_Commons_license#Seven_regularly_used_licenses');

define( 'SP_LICENSES', array(
	'' => 'none',
	'cc-by' => 'CC BY',
	'cc-sa' => 'CC BY-SA',
	'cc-nc' => 'CC BY-NC',
	'cc-nd' => 'CC BY-ND',
	'cc-nc-sa' => 'CC BY-NC-SA',
	'cc-nc-nd' => 'CC BY-NC-ND',
	'cc0' => 'CC0',
));

/*
	Initialize plugin
*/
function sp_video_posts_init() {

	require_once( SP_VIDEO_POST_PATH . '/classes/video-post-type.php' );
	require_once( SP_VIDEO_POST_PATH . '/classes/streampress-taxonomy-walker.php' );

    if ( is_admin() ) {
    	$GLOBALS['streampress_video_post']->admin_init();
    }

    require_once( SP_VIDEO_POST_PATH . '/public/frontend.php' );
}
add_action('plugins_loaded', 'sp_video_posts_init');


/*
	Initialize admin for video posts
*/
function sp_video_posts_admin() {

    if ( is_admin() ) {
    	$screen = get_current_screen();

    	if( $screen->post_type == 'sp_post_type' ) {
			$GLOBALS['streampress_video_post']->admin_init();
		}
    }
}
add_action('current_screen', 'sp_video_posts_admin');


/*
	Flush rewrite cache for video post type
*/
function sp_video_post_rewrite_flush() {

	require_once( SP_VIDEO_POST_PATH . '/classes/video-post-type.php' );

	// Initialize video post type
	$GLOBALS['streampress_video_post']->init();

    // Flush rewrite rules - Must be done only on activation
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'sp_video_post_rewrite_flush' );


/*
	Remove posts from admin (will be made into a configuration option)
*/
function sp_remove_menu_pages() {
	remove_menu_page('edit.php');
}
add_action( 'admin_menu', 'sp_remove_menu_pages' );

/*
	Enqueue Video.js scripts
*/
function sp_video_post_scripts() {

	wp_enqueue_script( 'sp-video-js', SP_VIDEO_POST_URL . 'static/js/video.js', array(), '6.2.7', false );
	wp_enqueue_script( 'sp-video-source-handler', SP_VIDEO_POST_URL . 'static/js/videojs5-hlsjs-source-handler.min.js', array(), '6.2.7', false );
	wp_enqueue_script( 'sp-video-quality-picker', SP_VIDEO_POST_URL . 'static/js/vjs-quality-picker.js', array(), '6.2.7', false );
	wp_enqueue_script( 'sp-video-thumbnails', SP_VIDEO_POST_URL . 'static/js/videojs.thumbnails.js', array(), '6.2.7', false );
	wp_enqueue_script( 'sp-video-playlist', SP_VIDEO_POST_URL . 'static/js/videojs-playlist.js', array(), '6.2.7', false );

	wp_enqueue_style( 'sp-video-css', SP_VIDEO_POST_URL . 'static/css/video-js.css', array(), '6.2.7', false );
	wp_enqueue_style( 'sp-video-thumbnail-css', SP_VIDEO_POST_URL . 'static/css/video-js-thumbnails.css', array(), '6.2.7', false );
}
add_action( 'wp_enqueue_scripts', 'sp_video_post_scripts' );


/*
	Setup plugin text domain
*/
function add_textdomain () {
	load_plugin_textdomain( 'streampress-video-post', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'add_textdomain' );


/*
	Init playlist taxonomy
*/
function init_playlist_taxonomy () {

	// Add new taxonomy, make it flat in hierarchy
	$labels = array(
		'name'              			=> _x( 'Playlists', 'taxonomy general name', 'streampress-video-post' ),
		'singular_name'     			=> _x( 'Playlist', 'taxonomy singular name', 'streampress-video-post' ),
		'search_items'      			=> __( 'Search Playlists', 'streampress-video-post' ),
		'all_items'         			=> __( 'All Playlists', 'streampress-video-post' ),
		'parent_item'       			=> __( 'Parent Playlist', 'streampress-video-post' ),
		'parent_item_colon' 			=> __( 'Parent Playlist:', 'streampress-video-post' ),
		'edit_item'         			=> __( 'Edit Playlist', 'streampress-video-post' ),
		'update_item'       			=> __( 'Update Playlist', 'streampress-video-post' ),
		'add_new_item'      			=> __( 'Add New Playlist', 'streampress-video-post' ),
		'new_item_name'     			=> __( 'New Playlist Name', 'streampress-video-post' ),
		'separate_items_with_commas'	=> __( 'Separate playlists with commas' ),
		'choose_from_most_used'			=> __( 'Choose from the most used playlists' ),
		'menu_name'         			=> __( 'Playlist', 'streampress-video-post' ),
	);

	$args = array(
		'hierarchical'      => false,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_tagcloud'		=> false,
		'show_admin_column' => false,
		'capabilities' 		=> array(
			'manage_terms' => 'publish_posts',
			'edit_terms'   => 'publish_posts',
			'delete_terms' => 'publish_posts',
			'assign_terms' => 'publish_posts'
		),
		'meta_box_cb'		=> 'sp_playlist_metabox_cb',
		'rewrite'           => array( 'slug' => 'playlist' ),
	);

	register_taxonomy( 'sp_playlist', 'sp_video_post', $args );
	register_taxonomy_for_object_type( 'sp_playlist', 'sp_video_post' );
}

add_action( 'init', 'init_playlist_taxonomy' );


function sp_playlist_metabox_cb( $post, $box ) {
	$r = array(
		'taxonomy' => 'sp_playlist'
	);

	$tax_name = esc_attr( $r['taxonomy'] );
	$taxonomy = get_taxonomy( $r['taxonomy'] );
	?>
	<div id="taxonomy-<?php echo $tax_name; ?>" class="categorydiv">
		<div id="<?php echo $tax_name; ?>-all" class="tabs-panel">
			<?php
			$name = ( $tax_name == 'category' ) ? 'post_category' : 'tax_input[' . $tax_name . ']';
			echo "<input type='hidden' name='{$name}[]' value='0' />"; // Allows for an empty term set to be sent. 0 is an invalid Term ID and will be ignored by empty() checks.

			$current_user = wp_get_current_user();

			?>
			<ul id="<?php echo $tax_name; ?>checklist" data-wp-lists="list:<?php echo $tax_name; ?>" class="categorychecklist form-no-clear">
				<?php wp_terms_checklist( $post->ID, array( 'taxonomy' => $tax_name, 'walker' => new Streampress_Taxonomy_Walker ) ); ?>
			</ul>
		</div>

		<p><?php echo sprintf( 'Select a playlist, or a create a <a href="%s">new one</a>.', admin_url( 'edit-tags.php?taxonomy=sp_playlist' ) ); ?></p>
	</div>
	<?php
}


/*
	Init video category taxonomy
*/
function init_video_category_taxonomy () {

	// Add video category taxonomy
	$labels = array(
		'name'              			=> _x( 'Categories', 'taxonomy general name', 'streampress-video-post' ),
		'singular_name'     			=> _x( 'Category', 'taxonomy singular name', 'streampress-video-post' ),
		'search_items'      			=> __( 'Search Categories', 'streampress-video-post' ),
		'all_items'         			=> __( 'All Categories', 'streampress-video-post' ),
		'parent_item'       			=> __( 'Parent Category', 'streampress-video-post' ),
		'parent_item_colon' 			=> __( 'Parent Category:', 'streampress-video-post' ),
		'edit_item'         			=> __( 'Edit Category', 'streampress-video-post' ),
		'update_item'       			=> __( 'Update Category', 'streampress-video-post' ),
		'add_new_item'      			=> __( 'Add New Category', 'streampress-video-post' ),
		'new_item_name'     			=> __( 'New Category Name', 'streampress-video-post' ),
		'separate_items_with_commas'	=> __( 'Separate categories with commas' ),
		'choose_from_most_used'			=> __( 'Choose from the most used categories' ),
		'menu_name'         			=> __( 'Category', 'streampress-video-post' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_tagcloud'		=> false,
		'show_admin_column' => false,
		'capabilities' 		=> array(
			'manage_terms' => 'update_core',
			'edit_terms'   => 'update_core',
			'delete_terms' => 'update_core',
			'assign_terms' => 'publish_posts'
		),
		'rewrite'           => array( 'slug' => 'video-category' ),
	);

	register_taxonomy( 'sp_video_category', 'sp_video_post', $args );
	register_taxonomy_for_object_type( 'sp_video_category', 'sp_video_post' );
}

add_action( 'init', 'init_video_category_taxonomy' );


// RSS feed for Streampress
function streampress_feed() {
	include( dirname( __FILE__ ) . '/rss/feed-template.php' );
}

/**
 * Adds an RSS feed to <site_url>/feed/streampress
 *
 * @param $query 	WP_Query
 */
function add_streampress_feed() {
	add_feed( 'streampress', 'streampress_feed' );

	// Requires one-time use of flush_rules() to take effect.
	// https://codex.wordpress.org/Rewrite_API/add_feed
	flush_rewrite_rules();
}
add_action( 'init', 'add_streampress_feed' );


/**
 * Filter the query for RSS feed.
 *
 * @param $query 	WP_Query
 */
function streampress_rss_filter( $query ) {

	if ( $query->is_feed() ) {

		$feed = get_query_var( 'feed', '' );

		// Only modify the Streampress RSS feed
		if ( $feed !== 'streampress' ) {

			$query->set( 'post_type', 'sp_video_post' );
			$query->set( 'orderby', 'modified' );
			$query->set( 'posts_per_page', 10 );
			$query->set( 'posts_per_rss', 10 );
		}
	}
}
add_action( 'pre_get_posts', 'streampress_rss_filter', 10, 1 );

/*
 *	Filter video post list - Only display video posts for current user
 *
 */
function sp_exclude_other_authors( $query ) {

	// restrict view for non-admin users
	if ( is_admin() && !current_user_can('administrator') ) {

		if( isset( $query->query_vars['post_type']) && $query->query_vars['post_type'] == 'sp_video_post' ) {
			$user = wp_get_current_user();
			$query->set( 'author', $user->ID );

			add_filter('views_edit-sp_video_post', 'sp_video_post_status_filter');
		}
	}
}
add_action( 'pre_get_posts', 'sp_exclude_other_authors' );


/*
 *	Restrict posts & pages admin views for non-admins
 */

function disallow_admin_pages() {
    global $pagenow;

    if( is_admin() && !current_user_can( 'administrator' ) ) {

    	if( $pagenow == 'edit.php' ) {

    		$type = empty( $_REQUEST['post_type'] ) ? 'post' : $_REQUEST['post_type'];

    		// Either posts or pages section
    		if ( $type == 'post' || $type == 'page' ) {
		        wp_redirect( home_url() );
		        exit;
    		}
	    }
    }
}
add_action( 'admin_init',  'disallow_admin_pages' );


/*
 *	Filter video post list - Only display video posts for current user
 *
 */
function sp_video_post_status_filter( $views ) {
    global $wp_query;

    // All posts are for current user / "mine" not needed
    unset($views['mine']);

    $user = wp_get_current_user();

    /*
     *	All posts filter
     */
    $query = new WP_Query( array(
        'post_type'   => 'sp_video_post',
        'post_status' => 'any',
    	'author'      => $user->ID
    ));

	$classes = isset( $wp_query->query_vars['post_status'] ) === false ? 'current' : '';

    $views['all'] = sprintf( __( '<a href="%s" class="' . $classes . '">All <span class="count">(%d)</span></a>', 'streampress' ),
        'edit.php?post_type=sp_video_post', $query->found_posts);


    /*
     *	Published posts filter
     */
    $query = new WP_Query( array(
        'post_type'   => 'sp_video_post',
        'post_status' => 'publish',
    	'author'      => $user->ID
    ));

    $classes = isset( $wp_query->query_vars['post_status'] ) && $wp_query->query_vars['post_status'] == 'publish' ? 'current' : '';

    $views['publish'] = sprintf( __( '<a href="%s" class="' . $classes . '">Published <span class="count">(%d)</span></a>', 'streampress' ),
        'edit.php?post_status=publish&post_type=sp_video_post', $query->found_posts);

    /*
     *	Draft posts filter
     */
    $query = new WP_Query( array(
        'post_type'   => 'sp_video_post',
        'post_status' => 'draft',
    	'author'      => $user->ID
    ));

    $classes = isset( $wp_query->query_vars['post_status'] ) && $wp_query->query_vars['post_status'] == 'draft' ? 'current' : '';

    $views['draft'] = sprintf( __( '<a href="%s" class="' . $classes . '">Drafts <span class="count">(%d)</span></a>', 'streampress' ),
        'edit.php?post_status=draft&post_type=sp_video_post', $query->found_posts);

    /*
     *	Pending posts filter
     */
    $query = new WP_Query( array(
        'post_type'   => 'sp_video_post',
        'post_status' => 'pending',
    	'author'      => $user->ID
    ));

    $classes = isset( $wp_query->query_vars['post_status'] ) && $wp_query->query_vars['post_status'] == 'pending' ? 'current' : '';

    $views['pending'] = sprintf( __( '<a href="%s" class="' . $classes . '">Pending <span class="count">(%d)</span></a>', 'streampress' ),
        'edit.php?post_status=pending&post_type=sp_video_post', $query->found_posts);

    /*
     *	Trash filter
     */
    $query = new WP_Query( array(
        'post_type'   => 'sp_video_post',
        'post_status' => 'trash',
    	'author'      => $user->ID
    ));

    $classes = isset( $wp_query->query_vars['post_status'] ) && $wp_query->query_vars['post_status'] == 'trash' ? 'current' : '';

    $views['trash'] = sprintf( __( '<a href="%s" class="' . $classes . '">Trash <span class="count">(%d)</span></a>', 'streampress' ),
        'edit.php?post_status=trash&post_type=sp_video_post', $query->found_posts);

    return $views;
}


/*
 *	Include current user's playlists in playlist admin
 */
function sp_list_terms_inclusions( $args, $taxonomies ) {

	if ( is_admin() && in_array( 'sp_playlist', $taxonomies ) ) {

		$current_user = wp_get_current_user();

	    $args['taxonomy'] = 'sp_playlist';
	    $args['hide_empty'] = false;
	    $args['meta_key'] = 'user_id';
	    $args['meta_value'] = $current_user->ID;
	}

    return $args;
}

add_filter( 'get_terms_args', 'sp_list_terms_inclusions', 10, 2 );


/*
 *	Add user meta data to a playlist when created
 */
add_action( 'create_term', 'sp_create_term_user_meta', 10, 3 );

function sp_create_term_user_meta( $term_id, $tt_id, $taxonomy ) {

	if ( $taxonomy == 'sp_playlist' ) {
		$current_user = wp_get_current_user();
		add_term_meta ( $term_id, 'user_id', $current_user->ID, true );
	}
}
