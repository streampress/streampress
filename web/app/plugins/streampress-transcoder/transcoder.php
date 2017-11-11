<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/Format/Video/CustomVideo.php');
require_once(__DIR__ . '/Format/Video/Customx264.php');
require_once(__DIR__ . '/Format/Video/480x264.php');
require_once(__DIR__ . '/Format/Video/720x264.php');
require_once(__DIR__ . '/Format/Video/m3u8Standard.php');

$input_video = '/Users/agoris/Downloads/skyfall-4k-ultra-hd-4ksamples-com.mp4';
$output_video = '/Users/agoris/Downloads/skyfall-4k-ultra-hd-4ksamples-com-' . date("Y-m-d-H-i-s") . '.mp4';
$output_m3u8 = '/Users/agoris/Downloads/skyfall-4k-ultra-hd-4ksamples-com-' . date("Y-m-d-H-i-s") . '.m3u8';

function videoDimensionInfo($input_video){
	$ffprobe = FFMpeg\FFProbe::create();
	$info = $ffprobe
	    ->streams($input_video) // extracts streams informations
	    ->videos()                      // filters video streams
	    ->first();   
	// $dimension = $ffprobe
	//     ->streams($input_video) // extracts streams informations
	//     ->videos()                      // filters video streams
	//     ->first()                       // returns the first video stream
	//     ->getDimensions(); 
	print_r($info);
}

function convertVideo($input_video, $output_video, $width, $height, $bitrate){
	// $ffmpeg = FFMpeg\FFMpeg::create();
	$ffmpeg = FFMpeg\FFMpeg::create(
		array(
			'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
			'ffprobe.binaries' => '/usr/local/bin/ffprobe',
			'timeout' => 0, // The timeout for the underlying process
			'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
		), 
		@$logger
	);
	$video = $ffmpeg->open($input_video);

	$dimension = new FFMpeg\Coordinate\Dimension($width, $height);
	// $video->filters()
 //      ->resize($dimension, RESIZEMODE_INSET, true, 1)
 //      ->synchronize();
	$video->filters()
          ->pad($dimension, \FFMpeg\Filters\Video\ResizeFilter::RESIZEMODE_INSET)
          ->synchronize();
	

	$format = new Customx264();

	$format->setAdditionalParameters(array('-force_key_frames', 'expr:gte(t,n_forced*10)'));

	$format->on('progress', function ($video, $format, $percentage) {
	    echo "$percentage % transcoded\n";
	});

	$format->setKiloBitrate($bitrate)
	       ->setAudioChannels(2)
	       ->setAudioKiloBitrate(256);

	$video->save($format, $output_video);
}

function videoScreenshot($input_video, $output_video, $width, $height, $bitrate){
	// $ffmpeg = FFMpeg\FFMpeg::create();
	$ffmpeg = FFMpeg\FFMpeg::create(
		array(
			'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
			'ffprobe.binaries' => '/usr/local/bin/ffprobe',
			'timeout' => 0, // The timeout for the underlying process
			'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
		), 
		@$logger
	);
	$video = $ffmpeg->open($input_video);
	$frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(42));
	$frame->save('image.jpg');

}

function videoScrubshots($input_video, $output_video, $width, $height, $bitrate){
	// $ffmpeg = FFMpeg\FFMpeg::create();
	$ffmpeg = FFMpeg\FFMpeg::create(
		array(
			'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
			'ffprobe.binaries' => '/usr/local/bin/ffprobe',
			'timeout' => 0, // The timeout for the underlying process
			'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
		), 
		@$logger
	);
	$video = $ffmpeg->open($input_video);
	$video->filters()
          ->extractMultipleFrames(FFMpeg\Filters\Video\ExtractMultipleFramesFilter::FRAMERATE_EVERY_10SEC, '/path/to/destination/folder/')
          ->synchronize();

    $format = new Customx264();

	$video->save($format, '/path/to/new/file');
	
}

function m3u8Create($input_video, $output_video, $width, $height, $bitrate){
	// $ffmpeg = FFMpeg\FFMpeg::create();
	$ffmpeg = FFMpeg\FFMpeg::create(
		array(
			'ffmpeg.binaries' => '/usr/local/bin/ffmpeg',
			'ffprobe.binaries' => '/usr/local/bin/ffprobe',
			'timeout' => 0, // The timeout for the underlying process
			'ffmpeg.threads' => 12, // The number of threads that FFMpeg should use
		), 
		@$logger
	);
	$video = $ffmpeg->open($input_video);
	//$video->addFilter(new SimpleFilter(['-g','60','-hls_time', '10' , '-hls_list_size', '0']));


	// $format = new FFMpeg\Format\Video\X264();
	
	// $format->on('progress', function ($video, $format, $percentage) {
	//     echo "$percentage % transcoded";
	// });

	// $format
 //    ->setKiloBitrate(1000)
 //    ->setAudioChannels(2)
 //    ->setAudioKiloBitrate(256);

	$video
	    ->hls(10, 0)
	    ->save($output_video);

	return true;

	//$video->save($format, $output_video);
}

// videoDimensionInfo($input_video);



// echo "Started: m3u8\n";
//m3u8Create($input_video, $output_m3u8, 256, 144, 200);
// echo "Completed: m3u8!\n";





// ffmpeg -i mexicantexan_border-ml.mp4 -g 60 -hls_time 10 -hls_list_size 0 out.m3u8
// echo "Started: 256, 144, 200 ...\n";
// convertVideo($input_video, $output_video144, 256, 144, 200);
// echo "Completed: 256, 144, 200!\n";

//Gif 
//$video->gif(TimeCode::fromSeconds( 3, new Dimension(640, 480), 3)
//https://github.com/PHP-FFMpeg/PHP-FFMpeg/issues/368



// $output_video234 = "/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/output-234.mp4";
// echo "Started: 416, 234, 400 ...\n";
// convertVideo($input_video, $output_video234, 416, 234, 400);
// echo "Completed: 416, 234, 400!\n";

// $output_video240 = "/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/output-240.mp4";
// echo "Started: 427, 240, 800 ...\n";
// convertVideo($input_video, $output_video240, 427, 240, 800);
// echo "Completed: 427, 240, 800!\n";

// $output_video360 = "/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/output-360.mp4";
// echo "Started: 640, 360, 1200 ...\n";
// convertVideo($input_video, $output_video360, 640, 360, 1200);
// echo "Completed: 640, 360, 1200!\n";

// $output_video432 = "/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/output-432.mp4";
// echo "Started: 768, 432, 2200 ...\n";
// convertVideo($input_video, $output_video432, 768, 432, 2200);
// echo "Completed: 768, 432, 2200!\n";

// $output_video480 = "/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/output-480.mp4";
// echo "Started: 854, 480, 2200 ...\n";
// convertVideo($input_video, $output_video480, 854, 480, 2200);
// echo "Completed: 854, 480, 2200!\n";

// convertVideo($input_video, $output_video, 960, 540, 3300);
// convertVideo($input_video, $output_video, 1280, 720, 5000);
// convertVideo($input_video, $output_video, 1280, 720, 6500);
// convertVideo($input_video, $output_video, 1920, 1080, 8600);
// convertVideo($input_video, $output_video, 2560, 1440, 8600);
// convertVideo($input_video, $output_video, 3840, 2160, 8600);


