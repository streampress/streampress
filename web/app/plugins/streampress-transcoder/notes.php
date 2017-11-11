<?php

// notes
// http://sinclairmediatech.com/encoding-hls-with-ffmpeg/

// command
// ffmpeg -y -i %(filename)s -pix_fmt yuv420p -vcodec libx264 -acodec libfdk_aac -r %(fps)s -profile:v baseline -b:v %(bitrate)sk -maxrate %(bitrate)sk  -force_key_frames %(keyframe_str)s -s %(width)sx%(height)s %-f segment -segment_list %(target_dir)sindex_%(bitrate)s.m3u8 -segment_time %(segment_size)s  -segment_list_type m3u8 %(filenameNoExt)s_%(count)s.ts

//$hlscommand = "ffmpeg -y -i %(filename)s -pix_fmt yuv420p -vcodec libx264 -acodec libfdk_aac -r %(fps)s -profile:v baseline -b:v %(bitrate)sk -maxrate %(bitrate)sk  -force_key_frames %(keyframe_str)s -s %(width)sx%(height)s %-f segment -segment_list %(target_dir)sindex_%(bitrate)s.m3u8 -segment_time %(segment_size)s  -segment_list_type m3u8 %(filenameNoExt)s_%(count)s.ts";

// working cli example
// ffmpeg -y -i infile.mp4 -pix_fmt yuv420p -vcodec libx264 -acodec libfdk_aac -r 25 -profile:v baseline -b:v 1500k -maxrate 2000k -force_key_frames 50 -s 640×360 -map 0 -flags -global_header -f segment -segment_list /tmp/index_1500.m3u8 -segment_time 10 -segment_format mpeg_ts -segment_list_type m3u8 /tmp/segment%05d.ts

// ffmpeg -y -i skyfall-4k-ultra-hd-4ksamples-com.mp4 -s 640x360 -pix_fmt yuv420p -vcodec libx264 -acodec libfdk_aac -r 25 -profile:v baseline -b:v 1500k -maxrate 2000k -force_key_frames 50  -map 0 -flags -global_header -f segment -segment_list /tmp/index_1500.m3u8 -segment_time 10 -segment_format mpeg_ts -segment_list_type m3u8 /tmp/segment%05d.ts

// ffmpeg -y -i skyfall-4k-ultra-hd-4ksamples-com.mp4 -s 640x360 -pix_fmt yuv420p -vcodec libx264 -acodec libfdk_aac -r 25 -profile:v baseline -b:v 1500k -maxrate 2000k -force_key_frames 50  -map 0 -flags -global_header -f segment -segment_list /tmp/index_1500.m3u8 -segment_time 10 -segment_format mpeg_ts -segment_list_type m3u8 /tmp/segment%05d.ts

// ffmpeg -y -i skyfall-4k-ultra-hd-4ksamples-com.mp4 -pix_fmt yuv420p -vcodec libx264 -acodec libfdk_aac -r 25 -profile:v baseline -b:v 1500k -maxrate 2000k -force_key_frames 50 -s 640x360 %-f segment -segment_list /tmp/index_1500.m3u8 -segment_time 10 -segment_list_type m3u8 segment_%05d.ts

// working echo
// ffmpeg -y -i skyfall-4k-ultra-hd-4ksamples-com.mp4 -pix_fmt yuv420p -vcodec libx264 -acodec libfdk_aac -r 25 -profile:v baseline -b:v 1500k -maxrate 2000k -force_key_frames 50 -s 640x360 -f segment -segment_list /tmp/index_1500.m3u8 -segment_time 10 -segment_list_type m3u8 segment_%05d.ts

// When you schedule Events, make sure to check the 60fps box in the Ingestion Settings tab. Stream now will auto-detect the frame rate and resolution.

// 4K / 2160p @60fps
// Resolution:  3840x2160p
// Framerate: 29.97
// Video Bitrate Range:  20,000 - 51,000 Kbps

// 4k / 2160p @30fps
// Resolution: 3840x2160p
// Framerate: 29.97
// Video Bitrate Range: 13,000 - 34,000 Kbps

// 1440p @60fps
// Resolution: 2560x1440
// Framerate: 29.97
// Video Bitrate Range: 9,000 - 18,000 Kbps

// 1440p @30fps
// Resolution: 2560x1440
// Framerate: 29.97
// Video Bitrate Range: 6,000 - 13,000 Kbps

// 1080p @60fps
// Resolution: 1920x1080
// Framerate: 29.97
// Video Bitrate Range: 4,500 - 9,000 Kbps

// 1080p
// Resolution: 1920x1080
// Framerate: 29.97
// Video Bitrate Range: 3,000 - 6,000 Kbps

// 720p @60fps
// Resolution: 1280x720
// Framerate: 29.97
// Video Bitrate Range: 2,250 - 6,000 Kbps

// 720p
// Resolution: 1280x720
// Framerate: 29.97
// Video Bitrate Range: 1,500 - 4,000 Kbps

// 480p
// Resolution: 854x480
// Framerate: 29.97
// Video Bitrate Range: 500 - 2,000 Kbps

// 360p
// Resolution: 640x360
// Framerate: 29.97
// Video Bitrate Range: 400 - 1,000 Kbps

// 240p
// Resolution: 426x240
// Framerate: 29.97
// Video Bitrate Range: 300 - 700 Kbps


// array("2160p", "3840", "2160", 20000, 51000, 60, 29.97, true); // youtube
// array("1440p", "2560", "1440", 9000, 18000, 60, 29.97, true); // youtube
// array("1080p", "1920", "1080", 4500, 9000, 60, 29.97, true); // youtube
// array("720p", "1280", "720", 2250, 6000, 60, 29.97, true); // youtube


// array("2160p", "3840", "2160", 13000, 34000, 30, 29.97, true); // youtube
// array("1440p", "2560", "1440", 6000, 13000, 30, 29.97, true); // youtube
// array("1080p", "1920", "1080", 3000, 6000, 30, 29.97, true); // youtube
// array("720p", "1280", "720", 1500, 4000, 30, 29.97, true); // youtube
// array("480p", "854", "480", 500, 2000, 30, 29.97, true); // youtube
// array("360p", "640", "360", 400, 1000, 30, 29.97, true); // youtube
// array("240p", "426", "240", 300, 700, 30, 29.97, true); // youtube
 

function hls_encode_command($params){

	$name = $params[0];
	$width = $params[1];
	$height = $params[2];
	$bitrate = $params[3];
	$maxbitrate = $params[4];
	$fps = $params[5];

	// $name = "360p";
	// $width = 640;
	// $height = 360;
	// $bitrate = 1500;
	// $maxbitrate = 2000;
	// $fps = 25;


	// variables
	$inputFilenameDir = "";
	$inputFilename = "skyfall-4k-ultra-hd-4ksamples-com.mp4";
	$keyframe_str = 50;
	//$target_dir = "/tmp/";
	$target_dir = "./";
	$segment_size = 10;
	$outputFilenameDir = "";
	$outputfilenameNoExt = "segment";
	$count = "%05d";
	$pix_fmt = "yuv420p";
	$vcodec = "libx264";
	$acodec = "libfdk_aac";
	$profile = "baseline";
	$f = "segment";

	$hlscommand = '';
	$hlscommand .= 'ffmpeg -y';
	$hlscommand .= ' -i ' . $inputFilename;
	$hlscommand .= ' -pix_fmt ' . $pix_fmt; 
	$hlscommand .= ' -vcodec ' . $vcodec;
	$hlscommand .= ' -acodec ' . $acodec; 
	$hlscommand .= ' -r ' . $fps;
	$hlscommand .= ' -profile:v ' . $profile; 
	$hlscommand .= ' -b:v ' . $bitrate . 'k';
	$hlscommand .= ' -maxrate ' . $maxbitrate . 'k';
	$hlscommand .= ' -force_key_frames ' . $keyframe_str;
	$hlscommand .= ' -s ' . $width . 'x' . $height;
	$hlscommand .= ' -f ' . $f;
	$hlscommand .= ' -segment_list ' . $target_dir . 'index_' . $bitrate .'.m3u8'; 
	$hlscommand .= ' -segment_time ' . $segment_size;  
	$hlscommand .= ' -segment_list_type m3u8 ' . $outputfilenameNoExt . '_' . $count . '.ts';

	return $hlscommand;
}

echo "\n";
// $variables = array("2160p", "3840", "2160", 13000, 34000, 30, 29.97, true); // youtube
// $variables = array("1440p", "2560", "1440", 6000, 13000, 30, 29.97, true); // youtube
// $variables = array("1080p", "1920", "1080", 3000, 6000, 30, 29.97, true); // youtube
$variables = array("720p", "1280", "720", 1500, 4000, 30, 29.97, true); // youtube
// $variables = array("480p", "854", "480", 500, 2000, 30, 29.97, true); // youtube
// $variables = array("360p", "640", "360", 400, 1000, 30, 29.97, true); // youtube
// $variables = array("240p", "426", "240", 300, 700, 30, 29.97, true); // youtube
echo hls_encode_command($variables);
echo "\n\n";





