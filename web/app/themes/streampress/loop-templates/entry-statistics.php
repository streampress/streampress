<?php
/**
 * Video post share / statistics
 *
 * @package understrap
 */
?>

<div class="row">
	<div class="col-12 col-sm-6 video-statistics">
		25k views
	</div>
	<div class="col-12 col-sm-6 social-container">
		<?php if ( shortcode_exists('likebtn') ):  ?>
			<div class="like-buttons">
				<?php echo do_shortcode('[likebtn theme="transparent" show_like_label="0" tooltip_enabled="0" popup_disabled="1"]'); ?>
			</div>
		<?php endif;  ?>
		<?php if ( shortcode_exists('addtoany') ):  ?>
			<div class="share-buttons">
				<?php echo do_shortcode('[addtoany url="'.get_permalink().'" title="'.get_the_title().'"]'); ?>
			</div>
		<?php endif;  ?>
	</div>
</div>