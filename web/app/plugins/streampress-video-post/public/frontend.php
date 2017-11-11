<?php

/**
 * Video post thumbnail data
 */
if ( !function_exists('sp_video_post_thumbnails') ) {

	function sp_video_post_thumbnails() {

		// Todo: After encoder is implemented
		// $thumbnails = get_post_meta( $post_id, 'sp_video_thumbnails', true );
		// ffmpeg -i skyfall-4k.mp4 -vf "fps=60/60, scale=136:136" thumb-%d.jpg

		// VideoJS Thumbnails
		$thumbnails = array();

		$i = 0;
		$interval = 4;	// in seconds
		$length = 300; 	// in seconds
		$base_url = get_template_directory_uri() . '/img/convert/';

		while ( $i <= $length) {

			$thumbnails[ $i * $interval ] = array(
				'src' => $base_url . 'thumb-' . ($i+1) .'.jpg'
			);

			// Add size information to first thumbnail
			if ( $i == 0 ) {
				$thumbnails[$i]['style'] = array(
					'left' => '0px',
					'width' => '68px'
				);
			}

			$i++;
		}
		?>

		<script>
			SP = SP || {};
			SP.thumbnails = <?php echo json_encode( $thumbnails ); ?>;
			console.log('SP.thumbnails', SP.thumbnails);
		</script>

		<?php
	}
}

/**
 * Video embed
 */
if ( !function_exists('sp_video_post_embed') ) {

	function sp_video_post_embed( $post_id ) {
		$url = get_post_meta( $post_id, 'sp_video_filename', true );
		$type = get_post_meta( $post_id, 'sp_video_type', true );

		if( !empty( $url ) ) {
			// Embed post thumbnail data
			add_action( 'wp_footer', 'sp_video_post_thumbnails', 10 );

		?>
			<video id="video-<?php echo $post_id; ?>" class="video-player video-js">
				<source src="<?php echo $url; ?>" type="<?php echo $type; ?>">
			</video>
		<?php
		}
	}
}

/**
 * Video description
 */
if ( !function_exists('sp_video_post_description') ) {

	function sp_video_post_description( $post_id ) {
		$text = get_post_meta( $post_id, 'sp_video_desc', true );

		$text = apply_filters( 'the_content', $text);

		if( !empty( $text ) ) {
		?>
			<?php echo $text; ?>
		<?php
		}
	}
}

/**
 * Video description
 */
if ( !function_exists('sp_video_post_category') ) {

	function sp_video_post_category( $post_id, $echo = true ) {
		$cats = get_the_terms( $post_id, 'sp_video_category' );
		$names = array();

		foreach( $cats as $cat ) {
			array_push( $names, '<a href="' . get_category_link( $cat->term_id ) .'">' . $cat->name . '</a>' );
		}

		$html = join( ', ', $names );

		if( !$echo ) {
			return $html;
		}

		if( $echo && !empty( $html ) ) {
			echo $html;
		}
	}
}

/**
 * Video license
 */
if ( !function_exists('sp_video_post_license') ) {

	function sp_video_post_license( $post_id ) {
		$key = get_post_meta( $post_id, 'sp_video_license', true );

		if ( isset( SP_LICENSES[$key] ) ) {
			echo esc_html( SP_LICENSES[$key] );
		}
	}
}