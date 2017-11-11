
// Main script for the theme
( function( window, document, $, videojs ) {
	'use strict';

	var slider_initialized = false;

	var setupCommentForm = function() {

	    var $form = $('#commentform');

	    $form.on( 'submit ', function( e ){
	    	e.preventDefault();

			var data = $form.serialize();

			$form.find('textarea').attr('value', '');

			$.ajax({
				url: $form.attr('action'),
				method: $form.attr('method'),
				data: data
			}).done( function() {
				updateComments( SP.current );
				$( window ).scrollTop( $('#form-title').offset().top );
			});

	    	return false;
	    });
	};

	var setupPlaylist = function() {

		var player = setupVideoPlayer();
		console.log( 'playlist', SP.playlist );

		player.playlist( SP.playlist.items );

		// Setup slider on resize
		var throttle_slider_setup = _.throttle( setupSlider, 100 );
		$( window ).on( 'resize.slider', throttle_slider_setup);

		setupSlider();
		setupReadmore();

		$('.playlist-item').on('click', function( e ) {
			e.preventDefault();

			var $item = $( e.currentTarget );

			$item.addClass('active').siblings().removeClass('active');

			var url = window.location.origin + window.location.pathname;
			var slug = $( this ).data( 'slug' );

			// Play selected video
			player.playlist.currentItem( $item.data( 'index' ) );

			// Update page URL
			history.replaceState( {}, "", url + "?v=" + slug );
			getPlaylistVideo( slug );
		});

		// Setup comment form
		setupCommentForm();

		// Play the selected video
		if ( SP.current ) {
			$('.thumbnail[data-slug="' + SP.current + '"]').trigger('click');
			return;
		}

		// Default: play first video
		player.playlist.autoadvance(0);
	};

	var getPlaylistVideo = function( slug ) {

		var playlist = SP.playlist.name;

		$.ajax({
			url: '/wp-json/wp/v2/video',
			method: 'GET',
			context: 'edit',
			data: {
				slug: slug,
				playlist: playlist
			},
			success: function( res ) {
				var data = _.first( res );
				console.log( 'API data', data );

				var title = data.title.rendered;
				var description = data.sp_video_desc;
				var share = data.sp_video_share;
				var categories = data.sp_video_categories ? data.sp_video_categories : '';
				var license = data.sp_video_license || 'none';
				var comments = data.comment_status ? data.sp_video_comments : '';

				$( '#comment_post_ID' ).attr( 'value', data.id );

				var $info = $( '.current-video-info' );

				$info.find( '.title' ).html( title );
				$info.find( '.video-description' ).html( description );
				$info.find( '.share-buttons' ).html( share );
				$info.find( '.video-categories' ).html( categories );
				$info.find( '.video-license' ).html( license );
				$info.find( '.comment-list' ).html( comments );

				setupReadmore();
				setupSocialShare();
			}
		});
	};

	var updateComments = function( slug ) {

		$.ajax({
			url: '/wp-json/wp/v2/video',
			method: 'GET',
			context: 'edit',
			data: {
				slug: slug
			},
			success: function( res ) {
				var data = _.first( res );
				var comments = data.comment_status ? data.sp_video_comments : '';
				var $info = $( '.current-video-info' );
				$info.find( '.comment-list' ).html( comments );
			}
		});
	};

	var resetPage = function() {
		console.log('reset');
		slider_initialized = false;
		$( window ).off( 'resize.slider' );
	};

	var setupSideMenu = function() {

		if( jQuery('#slideout').length ) {

			var slideout = new Slideout({
				'panel': document.getElementById('panel'),
				'menu': document.getElementById('slideout'),
				'duration': 230,
				'padding': 160,
				'tolerance': 70
			});

			$('.slideout-toggle').on( 'click', function() {
				slideout.toggle();
			});
		}
	};

	var setupSocialShare = function() {
		// social share update
		a2a.init('page');
	};

	/*
	 *	Slick slider setup
	 *  https://github.com/kenwheeler/slick
	 */
	var setupSlider = function() {

		if ( !slider_initialized ) {

			$( '.slider-playlist .slider-items' ).slick({
				dots: false,
				infinite: true,
				speed: 300,
				slidesToShow: 2,
				slidesToScroll: 2
			});

			$( '.slider-fullwidth .slider-items' ).slick({
				dots: false,
				infinite: true,
				speed: 300,
				slidesToShow: 4,
				slidesToScroll: 4,
				responsive: [{
					breakpoint: 1200,
					settings: {
						slidesToShow: 4,
						slidesToScroll: 4
					}
				}, {
					breakpoint: 992,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3
					}
				}, {
					breakpoint: 768,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				}, {
					breakpoint: 575,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					}
				}]
			});

			$( '.slider-halfwidth .slider-items' ).slick({
				dots: false,
				infinite: true,
				speed: 300,
				slidesToShow: 4,
				slidesToScroll: 4,
				responsive: [{
					breakpoint: 1200,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3
					}
				}, {
					breakpoint: 992,
					settings: {
						slidesToShow: 3,
						slidesToScroll: 3
					}
				}, {
					breakpoint: 768,
					settings: {
						slidesToShow: 2,
						slidesToScroll: 2
					}
				}, {
					breakpoint: 575,
					settings: {
						slidesToShow: 1,
						slidesToScroll: 1
					}
				}]
			});

			slider_initialized = true;
		}
	};

	/*
	*	Setup player for current video
	*
	*	Returns the videoJS player 		object
	*/
	var setupVideoPlayer = function() {

		var videoID = $('.video-player').attr('id');

		var player = videojs( videoID, {
			controls: true,
			autoplay: true,
			preload: 'auto',
			muted: true
		});

		// Video.js Quality Picker
		player.qualityPickerPlugin();

		// Video.js Thumbnails
		player.thumbnails( SP.thumbnails );

		return player;
	};

	var setupReadmore = function() {
		$('.video-description').readmore({
			lessLink: '',
			moreLink: '<a href="#" class="show-more-link">Show more</a>'
		});
	};

	var setupAutoplay = function() {

		$('.autoplay').on( 'click', function() {
			console.log( 'autoplay');
		});
	};

	var event = Turbolinks.supported ? 'turbolinks:load' : 'ready';

	$( document ).on( event, function() {
		console.log( 'ready', event );

		// Initial setup
		resetPage();
		setupSlider();
		setupSideMenu();
		setupSocialShare();

		// Video page
		if ( $( 'body' ).hasClass( 'single' ) ) {
			setupVideoPlayer();
			setupReadmore();
			setupAutoplay();
		}

		if ( $( 'body' ).hasClass( 'tax-sp_playlist' ) ) {
			setupPlaylist();
		}
	});

	$( document ).on( 'turbolinks:request-start', function() {

		// Dispose previously initiated players
		var keys = _.keys( videojs.players );

		_.each( keys, function( key ) {
			var oldPlayer = document.getElementById( key );

			if ( !!oldPlayer ) {
				videojs( oldPlayer ).dispose();
			}
		});
	});

})( window, document, jQuery, videojs );
