<?php

class Video_Post_Type {

	public $defaults = array(
		'video' => 'http://vjs.zencdn.net/v/oceans.mp4',
		'type' => 'video/mp4',
		'thumbnail' => 'https://jpylisela.com/thumb/thumb.jpg',
		'gif' => 'https://jpylisela.com/thumb/thumb.gif',
		'duration' => '3:35'
	);

    public function __construct() {
        add_action( 'init', array( &$this, 'init' ) );
    }

    /**
     * Register the custom post type
     */
    public function init() {
    	$this->register_video_post_type();
    	$this->add_custom_rest_fields();
    }

    public function register_video_post_type() {

	    register_post_type( 'sp_video_post',
	        array(
	            'labels' => array(
					'name' => __( 'Videos', 'streampress-video-post' ),
					'singular_name' => __( 'Video', 'streampress-video-post' ),
					'add_new' => __( 'Add New', 'streampress-video-post' ),
					'add_new_item' => __( 'Add New Video', 'streampress-video-post' ),
					'new_item' => __( 'New Video', 'streampress-video-post' ),
					'view_item' => __( 'View Video', 'streampress-video-post' ),
					'view_items' => __( 'View Videos', 'streampress-video-post' ),
					'featured_image' => __( 'Hero Image', 'streampress-video-post' ),
	            ),
	            'public'              => true,
	            'show_in_rest' 		  => true,
	            'rest_base'			  => 'video',
	            'has_archive'         => true,
	            'supports'            => array( 'title', 'comments' ),
	            'taxonomies'		  => array( 'video-category', 'playlist' ),
	            'rewrite'             => array( 'slug' => 'video' ),
	            'menu_name'           => __( 'Videos', 'streampress-video-post' ),
	            'menu_icon'           => 'dashicons-video-alt',
	            'menu_position'       => 5,
	            'can_export'          => true,
	            'has_archive'         => true,
	            'exclude_from_search' => false,
	            'publicly_queryable'  => true,
	            'capability_type'     => 'post'
	        )
	    );

	    remove_post_type_support( 'sp_video_post', 'editor' );
	    remove_post_type_support( 'sp_video_post', 'excerpt' );
    }

    /** Admin methods ******************************************************/

    /**
     * Initialize the admin, adding actions to properly display and handle
     * the sp_video_post custom post type add/edit page
     */
    public function admin_init() {
        global $post_type;

	    add_action( 'add_meta_boxes', array( &$this, 'meta_boxes' ) );
	    add_action( 'save_post', array( &$this, 'meta_boxes_save' ), 1, 2 );
    }

	public function video_post_get_meta( $object, $field_name, $request ) {

		// Description needs to include p tags
		if ( $field_name == 'sp_video_desc' ) {
			return apply_filters( 'the_content', get_post_meta( $object[ 'id' ], $field_name, true ));
		}

		// Shortcode for social share
		if ( $field_name == 'sp_video_share' ) {

			$link = get_permalink( $object[ 'id' ] );

			if( !empty( $request['playlist'] ) ) {
				$link =  site_url( "/playlist/" . strip_tags( $request['playlist'] ) . "?v=" . $object['slug'] );
			}

			return do_shortcode('[addtoany url="' . $link . '" title="'.get_the_title( $object[ 'id' ] ).'"]');
		}

		// License formatting
		if ( $field_name == 'sp_video_license' ) {
			return SP_LICENSES[get_post_meta( $object[ 'id' ], $field_name, true )];
		}

		if ( $field_name == 'sp_video_comments' ) {

			// Gather comments for a specific page/post
			$comments = get_comments(array(
				'post_id' => $object[ 'id' ],
				'status' => 'all'
			));

			// Display the list of comments
			return wp_list_comments( array(
				'per_page' => 10,
				'reverse_top_level' => false,
				'style'      => 'ol',
				'short_ping' => true,
				'echo' => false
			), $comments);
		}

		if ( $field_name == 'sp_video_categories' ) {
			return sp_video_post_category( $object[ 'id' ], false );
		}

	    return get_post_meta( $object[ 'id' ], $field_name, true );
	}

    /**
     *	Register REST API response fields
     */
	public function register_rest_fields() {

	    register_rest_field( 'sp_video_post',
	        'sp_video_desc',
	        array(
	            'get_callback'    => array( &$this, 'video_post_get_meta' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'sp_video_post',
	        'sp_video_share',
	        array(
	            'get_callback'    => array( &$this, 'video_post_get_meta' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'sp_video_post',
	        'sp_video_license',
	        array(
	            'get_callback'    => array( &$this, 'video_post_get_meta' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'sp_video_post',
	        'sp_video_comments',
	        array(
	            'get_callback'    => array( &$this, 'video_post_get_meta' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );

	    register_rest_field( 'sp_video_post',
	        'sp_video_categories',
	        array(
	            'get_callback'    => array( &$this, 'video_post_get_meta' ),
	            'update_callback' => null,
	            'schema'          => null,
	        )
	    );
	}

    /**
     *	Add custom REST API fields to response
     */
    public function add_custom_rest_fields() {
		add_action( 'rest_api_init', array( &$this, 'register_rest_fields' ) );
    }

    /**
     * Save meta boxes
     *
     * Runs when a video post is saved to update post meta
     */
    public function meta_boxes_save( $post_id, $post ) {

        if ( empty( $post_id ) || empty( $post ) || empty( $_POST ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( is_int( wp_is_post_revision( $post ) ) ) return;
        if ( is_int( wp_is_post_autosave( $post ) ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
        if ( $post->post_type != 'sp_video_post' ) return;

        $this->process_video_post_meta( $post_id, $post );
    }

    /**
     * Function for processing and storing all sp_video_post data.
     */
    private function process_video_post_meta( $post_id, $post ) {

		update_post_meta( $post_id, 'sp_video_filename', $_POST['sp_video_filename'] );
		update_post_meta( $post_id, 'sp_video_type', $_POST['sp_video_type'] );
		update_post_meta( $post_id, 'sp_video_thumbnail', $_POST['sp_video_thumbnail'] );
		update_post_meta( $post_id, 'sp_video_gif', $_POST['sp_video_gif'] );
		update_post_meta( $post_id, 'sp_video_desc', $_POST['sp_video_desc'] );
		update_post_meta( $post_id, 'sp_video_license', $_POST['sp_video_license'] );
		update_post_meta( $post_id, 'sp_video_duration', $_POST['sp_video_duration'] );
    }

    /**
     * Add thumbnail image for a video post
     */
    public function meta_boxes() {
        add_meta_box( 'video-posts-image', __( 'Video Logo', 'streampress-video-post' ), array( &$this, 'video_post_image' ), 'sp_video_post', 'side', 'high' );

         add_meta_box( 'sp_video_filename', __( 'Video URL', 'streampress-video-post' ), array( &$this, 'video_post_filename'), 'sp_video_post', 'normal', 'high' );

         add_meta_box( 'sp_video_type', __( 'Video type', 'streampress-video-post' ), array( &$this, 'video_post_type'), 'sp_video_post', 'normal', 'high' );

         add_meta_box( 'sp_video_thumbnail', __( 'Video thumbnail', 'streampress-video-post' ), array( &$this, 'video_post_thumbnail'), 'sp_video_post', 'normal', 'high' );

         add_meta_box( 'sp_video_gif', __( 'Video GIF', 'streampress-video-post' ), array( &$this, 'video_post_gif'), 'sp_video_post', 'normal', 'high' );

         add_meta_box( 'sp_video_description', __( 'Description', 'streampress-video-post' ), array( &$this, 'video_post_description'), 'sp_video_post', 'normal', 'high' );

         add_meta_box( 'sp_video_license', __( 'License', 'streampress-video-post' ), array( &$this, 'video_post_license'), 'sp_video_post', 'normal', 'low' );

         add_meta_box( 'sp_video_duration', __( 'Duration', 'streampress-video-post' ), array( &$this, 'video_post_duration'), 'sp_video_post', 'normal', 'low' );
    }

    /**
     * Display the thumbnail image meta box
     */
    public function video_post_image() {
        global $post;
        $image_src = SP_VIDEO_POST_URL . '/static/img/video-icon.jpg';
        ?>

        <img id="video-posts-image" src="<?php echo $image_src ?>" style="width: 80px; height: 80px" />

        <p>Video Thumbnail</p>

        <script type="text/javascript">
        jQuery(document).ready(function($) {

            $('#video-posts-image').on( 'click', function( e ) {
            	console.log('upload video here');
                return false;
            });

        });
        </script>
        <?php
    }

	/**
	 * Display the video filename metabox
	 */
	public function video_post_filename() {
	    global $post;
	    $value = get_post_meta( $post->ID, 'sp_video_filename', true );
	?>
	    <p>
	    	<label for="sp_video_filename">

	    		<?php printf( '%s: <ul><li>%s</li><li>%s</li><li>%s</li></ul>', esc_html__( 'Examples', 'streampress-video-post' ), '<i>http://vjs.zencdn.net/v/oceans.mp4</i>', '<i>https://bitdash-a.akamaihd.net/content/MI201109210084_1/m3u8s/f08e80da-bf1d-4e3d-8899-f0f6155f6efa.m3u8</i>', '<i>http://sample.vodobox.net/skate_phantom_flex_4k/skate_phantom_flex_4k.m3u8</i>' ); ?>

	    	</label>
	    	<br />
	        <input id="sp_video_filename" size="80" name="sp_video_filename" value="<?php echo empty($value) ? $this->defaults['video'] : $value ?>" />
	    </p>

	<?php
	}

	/**
	 * Display the video type metabox
	 */
	public function video_post_type() {
	    global $post;
	    $value = get_post_meta( $post->ID, 'sp_video_type', true );
	?>

	    <p>
	    	<label for="sp_video_type">

	    		<?php printf( '%s: <ul><li>%s</li><li>%s</li></ul>', esc_html__( 'Examples', 'streampress-video-post' ), '<i>video/mp4</i>', '<i>application/x-mpegURL</i>' ); ?>

	    	</label>
	    	<br />
	        <input id="sp_video_type" size="80" name="sp_video_type" value="<?php echo empty($value) ? $this->defaults['type'] : $value ?>" />
	    </p>

	<?php
	}

	/**
	 * Display the video thumbnail metabox
	 */
	public function video_post_thumbnail() {
	    global $post;
	    $value = get_post_meta( $post->ID, 'sp_video_thumbnail', true );
	?>

	    <p>
	    	<label for="sp_video_thumbnail">

	    		<?php printf( '%s: <ul><li>%s</li><li>%s</li></ul>', esc_html__( 'Examples', 'streampress-video-post' ), '<i>https://jpylisela.com/thumb/thumb.jpg</i>', '<i>https://jpylisela.com/thumb/thumb-2.jpg</i>' ); ?>

	    	</label>
	    	<br />
	        <input id="sp_video_thumbnail" size="80" name="sp_video_thumbnail" value="<?php echo empty($value) ? $this->defaults['thumbnail'] : $value ?>" />
	    </p>

	<?php
	}

	/**
	 * Display the video thumbnail GIF
	 */
	public function video_post_gif() {
	    global $post;
	    $value = get_post_meta( $post->ID, 'sp_video_gif', true );
	?>

	    <p>
	    	<label for="sp_video_gif">

	    		<?php printf( '%s: <ul><li>%s</li><li>%s</li></ul>', esc_html__( 'Examples', 'streampress-video-post' ), '<i>https://jpylisela.com/thumb/thumb.gif</i>', '<i>https://jpylisela.com/thumb/thumb-2.gif</i>' ); ?>

	    	</label>
	    	<br />
	        <input id="sp_video_gif" size="80" name="sp_video_gif" value="<?php echo empty($value) ? $this->defaults['gif'] : $value ?>" />
	    </p>

	<?php
	}

	public function video_post_description( $post ) {

		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'myplugin_noncename' );

		$field_value = get_post_meta( $post->ID, 'sp_video_desc', true );

		wp_editor( $field_value, 'sp_video_desc', array(
			'wpautop' => true,
			'tinymce' => true
		));
	}

	/**
	 * Display the video license metabox
	 */
	public function video_post_license() {
	    global $post;
	    $current = get_post_meta( $post->ID, 'sp_video_license', true );
	?>
	    <p>
	    	<?php echo esc_html__( 'Copyright license for the video', 'streampress-video-post' ); ?>.

	    	<i>
		    	<?php echo esc_html__( 'Read more about', 'streampress-video-post' ) . " "; ?>

		    	<?php printf( '<a href="%s" target="_blank">%s</a>', SP_LICENSE_URL, strtolower( __('Licenses', 'streampress-video-post') ));
		    	?>
	    	</i>
	    </p>

	    <div>
	    	<?php foreach( array_keys( SP_LICENSES ) as $key ): ?>
	    		<input type="radio" name="sp_video_license" value="<?php echo $key ?>" <?php if( $current == $key ): ?>checked<?php endif; ?>> <?php echo SP_LICENSES[$key]; ?> <br>
	    	<?php endforeach; ?>
	    </div>
	<?php
	}

	/**
	 * Video duration
	 */
	public function video_post_duration() {
	    global $post;
	    $current = get_post_meta( $post->ID, 'sp_video_duration', true );
	?>
	    <p>
	    	<?php echo esc_html__( 'Duration of the video', 'streampress-video-post' ); ?>.
	    </p>

	    <div>
			<input type="text" name="sp_video_duration" value="<?php echo empty( $current ) ? $this->defaults['duration'] : esc_html($current) ?>" />
			<br>
	    </div>
	<?php
	}
}

// Instantiate plugin class and add to globals
$GLOBALS['streampress_video_post'] = new Video_Post_Type();
