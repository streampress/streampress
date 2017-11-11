<?php
/*
Plugin Name: StreamPress Transcoder
Plugin URI: TBD
Description: Provides StreamPress Transcoder ffmpeg. 
Version: 1.0
Author: StreamPress
Author URI: http://www.streampress.io/tech
Text Domain: streampress-transcoder
*/

//TODO 
// add bitrate support for hls
// fix recursive delete
// fork the ffmpeg library
	// look into custom commands
// create gif creation function
// create scrub videos
// create screenshot
// add timeouts


// gif play
// https://superuser.com/questions/556029/how-do-i-convert-a-video-to-gif-using-ffmpeg-with-reasonable-quality
//sp_add_gif_size("4K", "1280", "720", $duration, start, end);
// ffmpeg -ss 30 -t 6 -i skyfall-4k-ultra-hd-4ksamples-com.mp4 -i palette.png -filter_complex "fps=10,scale=320:-1:flags=lanczos[x];[x][1:v]paletteuse" skyfall-4k-ultra-hd-4ksamples-com.gif
// ffmpeg -y -ss 30 -t 3 -i skyfall-4k-ultra-hd-4ksamples-com.mp4 -vf fps=10,scale=320:-1:flags=lanczos,palettegen palette.png
// skyfall-4k-ultra-hd-4ksamples-com


// https://superuser.com/questions/538112/meaningful-thumbnails-for-a-video-using-ffmpeg
//sp_add_thumbnail_size("4K", "1280", "720", $duration, start, end);


// scrub add length of video
//sp_add_scrub_size("4K", "1280", "720", $duration, start, end);

// https://trac.ffmpeg.org/wiki/Create%20a%20thumbnail%20image%20every%20X%20seconds%20of%20the%20video
//ffmpeg -i skyfall-4k-ultra-hd-4ksamples-com.mp4 -vf fps=4 skyfall-4k-ultra-hd-4ksamples-com-%d.png
 

// https://superuser.com/questions/538112/meaningful-thumbnails-for-a-video-using-ffmpeg
// ffmpeg -ss 3 -i skyfall-4k-ultra-hd-4ksamples-com.mp4 -vf "select=gt(scene\,0.4)" -frames:v 5 -vsync vfr -vf fps=fps=1/600 skyfall-4k-ultra-hd-4ksamples-com-%02d.jpg



require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/Format/Video/CustomVideo.php');
require_once(__DIR__ . '/Format/Video/Customx264.php');
require_once(__DIR__ . '/Format/Video/480x264.php');
require_once(__DIR__ . '/Format/Video/720x264.php');
require_once(__DIR__ . '/Format/Video/m3u8Standard.php');
require_once(__DIR__ . '/setup.php');
require_once(__DIR__ . '/transcoder.php');
require_once(__DIR__ . '/admin/admin.php');

// Functon to add an item to the queue
function streampress_videos_data_add($queue_array = array()) {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'streampress_videos';

	$resize = $queue_array['resize'];
	$path = $queue_array['new_fullsize_path'] . '/' . $queue_array['resize'];

	$wpdb->insert( 
		$table_name, 
		array( 
			'attachment_id' => $queue_array['attachment_id'],
			'gallery' => "gallery",
			'resize' => $resize,
			'path' => $path,
			'converted' => "converted",
			'results' => "results",
			'image_size' => 0,
			'orig_size' => 0,
			'backup' => "backup",
			'level' => 0,
			'pending' => 0,
			'updates' => 0,
			'updated' => current_time( 'mysql' ),
			'trace' => Null
		) 
	);
}

// Create a video directory
function create_vid_directory($attachmentid, $filename, $fullfilepath){
	//create a new directory for the transcoded videos

	// path
	$parts = explode('/', $fullfilepath);
	array_pop($parts);
	$new_fullsize_path = implode('/', $parts);
	
	// file name
	$fileWithoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);

	$vid_directory =  $new_fullsize_path . '/' . 'vid-' . $fileWithoutExt;

	if (wp_mkdir_p($vid_directory)) {
		add_post_meta( $attachmentid, '_vid_directory', $vid_directory );
		return $vid_directory;
	}

	return "Error";
}

function sp_add_video_size( $name, $width = 0, $height = 0, $bitrate = 0, $maxbitrate = 0, $crop = false ) {
    global $_sp_additional_video_sizes;
 
    $_sp_additional_video_sizes[ $name ] = array(
        'width'  => absint( $width ),
        'height' => absint( $height ),
        'bitrate' => absint( $bitrate ),
        'crop'   => $crop,
    );
}

sp_add_video_size("144p", "256", "144", 10000, 10000, true); // youtube
sp_add_video_size("240p", "426", "240", 10000, 10000, true); // youtube
sp_add_video_size("360p", "640", "360", 10000, 10000, true); // youtube
sp_add_video_size("480p", "854", "480", 10000, 10000, true); // youtube
sp_add_video_size("720p", "1280", "720", 10000, 10000, true); // youtube
sp_add_video_size("1080p", "1920", "1080", 10000, 10000, true); // youtube
sp_add_video_size("1440p", "2560", "1440", 10000, 10000, true); // youtube
sp_add_video_size("4K", "3840", "2160", 10000, 10000, true); // youtube

function deleteDirectory($dir) { 
    if (!file_exists($dir)) { return true; }
    if (!is_dir($dir) || is_link($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) { 
        if ($item == '.' || $item == '..') { continue; }
        if (!deleteDirectory($dir . "/" . $item, false)) { 
            chmod($dir . "/" . $item, 0777); 
            if (!deleteDirectory($dir . "/" . $item, false)) return false; 
        }; 
    } 
    return rmdir($dir); 
}

function removeDirectory($path) {
 	$files = glob($path . '/*');
	foreach ($files as $file) {
		is_dir($file) ? removeDirectory($file) : unlink($file);
	}
	rmdir($path);
 	return;
}

/* 
 * php delete function that deals with directories recursively
 */
function delete_files($target) {
    if(is_dir($target)){
        $files = glob( $target . '*', GLOB_MARK ); //GLOB_MARK adds a slash to directories returned
        
        foreach( $files as $file )
        {
            delete_files( $file );      
        }
      
        rmdir( $target );
    } elseif(is_file($target)) {
        unlink( $target );  
    }
}

// delete_files('/path/for/the/directory/');

function delete_vid_directory($attachmentid){
	global $wpdb;
	global $wp_filesystem;

	//create a new directory for the transcoded videos
	$_vid_directory = get_post_meta( $attachmentid, $key = '_vid_directory', true );
	$table_name = $wpdb->prefix . 'streampress_videos';


	//WP_Filesystem_Base->rmdir;
	//TODO: Recursive
	//if ($wp_filesystem->rmdir($_vid_directory, true)) {
	if (rmdir($_vid_directory)) {
		// TODO
		// MYSQL DELETE THE ITEM in wp_streampress_videos where attachment_id = $attachmentid
		$wpdb->delete( $table_name, array( 'attachment_id' => $attachmentid ) );
		return true;
	}

	return true;
}

add_action('add_attachment', 'streampress_attachment_add');
function streampress_attachment_add($attachment_id){

	global $_sp_additional_video_sizes;

	if (wp_attachment_is( 'video', $attachment_id )){

		$the_attachement = get_post($attachment_id);
		$the_attachement_name = $the_attachement->post_name;

		//$_wp_attached_file = get_post_meta( $id, $key = '_wp_attached_file', true );

		$fullsize_path = get_attached_file( $attachment_id );

		$new_fullsize_path = create_vid_directory($attachment_id, $the_attachement_name,$fullsize_path);
		

		// add files to the queue for transcoding
		$sp_setttings = array(
			'attachment_id' => $attachment_id,
			'new_fullsize_path' => $new_fullsize_path,
			'size_name' => 'Full',
			'the_attachement_name' => $the_attachement_name
		);


		foreach ($_sp_additional_video_sizes as $key => $value) {
			$sp_setttings['resize'] = $key;
			streampress_videos_data_add($sp_setttings);
			// print_r( $key);
			// print_r($value['width']);
			// add files to the queue for transcoding
		}

		//streampress_videos_data_add($sp_setttings);
		//streampress_videos_data_add($sp_setttings);
		//streampress_videos_data_add($attachment_id, $new_fullsize_path, 'Full', $the_attachement_name);

	}

}

add_action('delete_attachment', 'streampress_attachment_delete');
function streampress_attachment_delete($attachment_id)
{
	if (wp_attachment_is( 'video', $attachment_id )){
		// $the_attachement = get_post($attachment_id);
		// $the_attachement->post_name;
		delete_vid_directory($attachment_id);
	}
}




function convert_video(){
	global $wpdb;
	global $_sp_additional_video_sizes;

	$table_name = $wpdb->prefix . 'streampress_videos';

	$sql = "SELECT * FROM $table_name WHERE converted = 'pending' LIMIT 1";
	$results = $wpdb->get_row($sql);

	$resize = $results->resize;

	$thesize = $_sp_additional_video_sizes[ $resize ];

	$input_video = get_attached_file( $results->attachment_id );

	print_r($input_video);
	print_r("jackson 5");
	print_r($results);
	print_r($thesize);

	$output_m3u8 = $results->path . '/vid-skyfall-4k' . '-' . $thesize['width'] .  'x' . $thesize['height'] . '_.m3u8';


	print_r($input_video);
	echo "\n";
	print_r($output_m3u8);
	echo "\n";
	print_r($thesize['width']);
	echo "\n";
	print_r($thesize['height']);
	echo "\n";
	print_r($thesize['bitrate']);

	if (wp_mkdir_p($results->path)) {

		$wpdb->update( $table_name, array( 'converted' => 'inprogress' ), array( 'ID' => $results->id ) );

		$conversion = m3u8Create($input_video, $output_m3u8, $thesize['width'], $thesize['height'], $thesize['bitrate']);

		if ($conversion == true){
			$post_meta_key = '_vid_m3u8_' . $resize;


			add_post_meta( $results->attachment_id, $post_meta_key, $output_m3u8 );


			$wpdb->update( $table_name, array( 'converted' => 'complete' ), array( 'ID' => $results->id ) );
		}else{
			$wpdb->update( $table_name, array( 'converted' => 'failed' ), array( 'ID' => $results->id ) );
		}

		//$wpdb->get_results;

		
	}
}

//convert_video();
//delete_vid_directory(95);

