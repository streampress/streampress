<?php
/**
 * Left sidebar check.
 *
 * @package understrap
 */
?>

<?php if ( is_active_sidebar( 'sidebar-right' ) ): ?>
	<div class="col-12 col-lg-8 content-area" id="primary">
<?php else: ?>
	<div class="col-12 content-area" id="primary">
<?php endif; ?>
