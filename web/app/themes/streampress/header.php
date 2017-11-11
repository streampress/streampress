<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package understrap
 */
$search_term = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?> <?php if( is_home() ): ?>class="slideout-open"<?php endif; ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<script>
		var SP = SP || {};
	</script>

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div class="hfeed site">

	<?php get_template_part( 'global-templates/slideout-menu' ); ?>

	<div class="panel-container" id="panel">

		<!-- ******************* The Navbar Area ******************* -->
		<div class="wrapper-fluid wrapper-navbar" id="wrapper-navbar">

			<a class="skip-link screen-reader-text sr-only" href="#content"><?php esc_html_e( 'Search',
			'streampress' ); ?></a>

			<nav class="navbar navbar-expand-md navbar-light">

				<!-- Burger icon -->
				<i class="slideout-toggle icon fa fa-reorder"></i>

				<!-- Site title as branding in the menu -->
				<?php if ( ! has_custom_logo() ) { ?>

					<?php if ( is_front_page() && is_home() ) : ?>
						<i class="site-logo icon fa fa-play-circle"></i>

						<div class="navbar-brand mb-0">
							<?php bloginfo( 'name' ); ?>
						</div>

					<?php else : ?>
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-logo icon fa fa-play-circle"></a>

						<a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php bloginfo( 'name' ); ?></a>

					<?php endif; ?>


				<?php } else {
					the_custom_logo();
				} ?><!-- end custom logo -->

				<!-- Search -->
				<div class="search-container">
					<form action="/" method="get">
						<div class="input-group">
							<input type="text" class="form-control search-input" name="s" value="<?php echo esc_html( $search_term ); ?>" placeholder="<?php esc_html_e( 'Search',
				'streampress' ); ?>">
							<span class="input-group-btn">
								<button class="btn btn-secondary search-btn" type="button">
									<i class="icon fa fa-search"></i>
								</button>
							</span>
						</div>
					</form>
				</div>

				<div class="header-btn-container">
					<?php if( is_user_logged_in() ): ?>
					<!-- Logged in -->
					<a href="<?php echo wp_logout_url( home_url('/') ); ?>" class="btn btn-primary btn-sm btn-register" role="button">
						<?php echo esc_html__( 'Logout', 'streampress' )?>
					</a>

					<?php else: ?>
					<!-- Logged out -->
					<a href="<?php echo wp_login_url( home_url('/') ); ?>" class="btn btn-primary btn-sm btn-login" role="button">
						<?php echo esc_html__( 'Login', 'streampress' )?>
					</a>
					<a href="<?php echo esc_url( home_url('/register') ); ?>" class="btn btn-primary btn-sm btn-register" role="button">
						<?php echo esc_html__( 'Register', 'streampress' )?>
					</a>
					<?php endif; ?>
				</div>

			</nav><!-- .site-navigation -->

		</div><!-- .wrapper-navbar end -->
