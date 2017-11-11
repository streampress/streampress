<?php

global $streampress_db_version;
$streampress_db_version = '1.0';

// Create Database
function streampress_install() {
	global $wpdb;
	global $streampress_db_version;

	$table_name = $wpdb->prefix . 'streampress_videos';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		`id` int(14) unsigned NOT NULL AUTO_INCREMENT,
		`attachment_id` bigint(20) unsigned DEFAULT NULL,
		`function` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		`gallery` varchar(10) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		`resize` varchar(75) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		`image_size` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`path` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`converted` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`results` varchar(75) COLLATE utf8mb4_unicode_520_ci NOT NULL,
		`video_size` int(10) unsigned DEFAULT NULL,
		`orig_size` int(10) unsigned DEFAULT NULL,
		`backup` varchar(100) COLLATE utf8mb4_unicode_520_ci DEFAULT NULL,
		`level` int(5) unsigned DEFAULT NULL,
		`pending` tinyint(1) NOT NULL DEFAULT '0',
		`updates` int(5) unsigned DEFAULT NULL,
		`updated` timestamp NULL DEFAULT '1971-01-01 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
		`trace` blob,
		UNIQUE KEY `id` (`id`),
		KEY `path_image_size` (`path`(191),`video_size`),
		KEY `attachment_info` (`gallery`(3),`attachment_id`),
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'streampress_db_version', $streampress_db_version );
}
register_activation_hook( __FILE__, 'streampress_install' );

// Remove Database
function streampress_uninstall() {
     global $wpdb;
     global $streampress_db_version;

     $table_name = $wpdb->prefix . "streampress_videos";
     $sql = "DROP TABLE IF EXISTS $table_name;";
     $wpdb->query($sql);
     delete_option($streampress_db_version);
}
register_deactivation_hook( __FILE__, 'streampress_uninstall' );