<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FFMpeg\Media;

use Alchemy\BinaryDriver\Exception\ExecutionFailureException;
use FFMpeg\Filters\Hls\HlsFilterInterface;
use FFMpeg\Filters\Hls\HlsFilters;
use FFMpeg\Driver\FFMpegDriver;
use FFMpeg\FFProbe;
use FFMpeg\Exception\RuntimeException;

// ffmpeg -i mexicantexan_border-ml.mp4 -g 60 -hls_time 10 -hls_list_size 0 out.m3u8
// '/usr/local/bin/ffmpeg' '-y' '-ss' '00:00:10.00' '-i' '/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/taipei_101_fireworks_trailer.mp4' '-vframessf' '1' '-f' 'image2' '/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/frame.jpg'

class Hls extends AbstractMediaType
{
    /** @var HlsTime */
    private $hlstime;
    /** @var HlsListSize */
    private $hlslistsize;
    /** @var Video */
    private $video;

    public function __construct(Video $video, FFMpegDriver $driver, FFProbe $ffprobe, $hlstime, $hlslistsize)
    {
        parent::__construct($video->getPathfile(), $driver, $ffprobe);
        $this->hlstime = $hlstime;
        $this->hlslistsize = $hlslistsize;
        $this->video = $video;
    }

    /**
     * Returns the video related to the frame.
     *
     * @return Video
     */
    public function getVideo()
    {
        return $this->video;
    }

    /**
     * {@inheritdoc}
     *
     * @return HlsFilters
     */
    public function filters()
    {
        return new HlsFilters($this);
    }

    /**
     * {@inheritdoc}
     *
     * @return Frame
     */
    public function addFilter(HlsFilterInterface $filter)
    {
        $this->filters->add($filter);

        return $this;
    }

    /**
     * @return HlsTime
     */
    public function getHlsTime()
    {
        return $this->hlstime;
    }

    /**
     * @return HlsListSize
     */
    public function getHlsListSize()
    {
        return $this->hlslistsize;
    }

    /**
     * Saves the frame in the given filename.
     *
     * Uses the `unaccurate method by default.`
     *
     * @param string  $pathfile
     * @param Boolean $accurate
     *
     * @return Frame
     *
     * @throws RuntimeException
     */
    public function save($pathfile, $accurate = false)
    {
        /**
         * might be optimized with http://ffmpeg.org/trac/ffmpeg/wiki/Seeking%20with%20FFmpeg
         * @see http://ffmpeg.org/ffmpeg.html#Main-options
         */
        // https://docs.peer5.com/guides/production-ready-hls-vod/
        if (!$accurate) {
            $commands = array(
                '-i', $this->pathfile,
                '-g', (string) 60,
                '-hls_time', (string) $this->hlstime,
                '-hls_list_size', (string) $this->hlslistsize
            );
        } else {
            echo "save else";
            // $commands = array(
            //     '-y', '-i', $this->pathfile,
            //     '-vframes', '1', '-ss', (string) $this->timecode,
            //     '-f', $outputFormat
            // );
        }

        foreach ($this->filters as $filter) {
            $commands = array_merge($commands, $filter->apply($this));
        }

        $commands = array_merge($commands, array($pathfile));

        print_r($commands);

        try {
            $this->driver->command($commands);
            return $this;
        } catch (ExecutionFailureException $e) {
            $this->cleanupTemporaryFile($pathfile);
            throw new RuntimeException('Unable to save frame', $e->getCode(), $e);
        }
    }
}
