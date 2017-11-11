<?php
/**
 * RSS feed for Streampress
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 *
 * @package default
 */

header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );
echo '<?xml version="1.0" encoding="' . esc_attr( get_option( 'blog_charset' ) ) . '"?' . '>';
$last_modified = null;

$videos = new WP_Query( array(
	'post_type' => 'sp_video_post',
	'limit' => 100
));
?>
<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
	<channel>
		<title><?php bloginfo_rss( 'name' ); ?> - Streampress Videos</title>
		<link><?php bloginfo_rss( 'url' ) ?></link>
		<description><?php bloginfo_rss( 'description' ) ?></description>
		<?php while ( $videos->have_posts() ) : $videos->the_post(); ?>
			<item>
				<title><?php echo esc_html( get_the_title() ); ?></title>
				<link><?php echo esc_url( wp_get_canonical_url( get_the_ID() ) ); ?></link>
				<content:encoded>
					<![CDATA[<?php echo esc_html( get_the_title() ); ?>]]>
				</content:encoded>
				<guid isPermaLink="false"><?php esc_html( the_guid() ); ?></guid>
				<description><![CDATA[<?php echo esc_html( get_the_title() ); ?>]]></description>
				<pubDate><?php echo esc_html( get_the_date("j M Y") ); ?></pubDate>
				<modDate><?php echo esc_html( get_the_modified_date("j M Y") ); ?></modDate>
				<author><?php echo esc_html( get_the_author() ); ?></author>
			</item>
		<?php endwhile; ?>
		<?php wp_reset_postdata(); ?>
		<?php if ( ! is_null( $last_modified ) ) : ?>
			<lastBuildDate><?php echo esc_html( $last_modified ); ?></lastBuildDate>
		<?php endif; ?>
	</channel>
</rss>
