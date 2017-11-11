<?php     

// https://stackoverflow.com/questions/37347894/hls-live-streaming-from-static-files
// https://paulund.co.uk/php-delete-directory-and-files-in-directory

$myfile = fopen($this->raw_path."/".$this->file_name.".m3u8", "w") or die("Unable to open file!");

$txt = "#EXTM3U\n";

fwrite($myfile, $txt);

$txt = "#EXT-X-VERSION:3\n";

fwrite($myfile, $txt);
// fclose($myfile);
if($convertedRes['720']){
        $txt = "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=73056000,RESOLUTION=1280x720\n";
        fwrite($myfile, $txt);
        $txt = $this->file_name."/".$this->file_name."-720.m3u8\n";
        fwrite($myfile, $txt);
}

if($convertedRes['480']){
        $txt = "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=5605600,RESOLUTION=854x480\n";
        fwrite($myfile, $txt);
        $txt = $this->file_name."/".$this->file_name."-480.m3u8\n";
        fwrite($myfile, $txt);
}

if($convertedRes['360']){
        $txt = "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=2855600,RESOLUTION=640x360\n";
        fwrite($myfile, $txt);
        $txt = $this->file_name."/".$this->file_name."-360.m3u8\n";
        fwrite($myfile, $txt);
}

if($convertedRes['240']){
        $txt = "#EXT-X-STREAM-INF:PROGRAM-ID=1,BANDWIDTH=1755600,RESOLUTION=428x240\n";
        fwrite($myfile, $txt);
        $txt = $this->file_name."/".$this->file_name."-240.m3u8\n";
        fwrite($myfile, $txt);
}
fclose($myfile);