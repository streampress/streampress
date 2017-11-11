<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

$the_theme = wp_get_theme();
?>

<?php get_sidebar( 'footerfull' ); ?>

<!-- Social sharing setup -->
<script type="text/javascript">
	console.log('setupSocialTracking');

	function track_social_shares (data) {
		console.log( 'share', data );
	}

	// Setup AddToAny "onReady" and "onShare" callback functions
	var a2a_config = a2a_config || {};

	a2a_config.callbacks = a2a_config.callbacks || [];

	a2a_config.callbacks.push({
	    share: track_social_shares
	});
</script>


<div class="wrapper footer" id="wrapper-footer">

	<div class="container-fluid">

		<div class="row">

			<div class="col-md-12">

				<footer class="site-footer" id="colophon">

					<div class="site-info text-center">

						<?php echo esc_html__( 'Powered by', 'streampress' ); ?>

						<a href="<?php echo esc_url( 'http://www.clickon.co/tech', 'streampress' ); ?>" target="_blank">
							<?php echo esc_html__( 'Streampress', 'streampress' ); ?>
						</a>

					</div><!-- .site-info -->

				</footer><!-- #colophon -->

			</div><!--col end -->

		</div><!-- row end -->

	</div><!-- container end -->

</div><!-- wrapper end -->

</div><!-- panel container end -->

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>
