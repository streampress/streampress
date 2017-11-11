<?php
/**
 * The m3u8Standard video format
 */

class m3u8Standard extends FFMpeg\Format\Video\DefaultVideo 
{
    /** @var boolean */
    private $bframesSupport = true;

    /** @var integer */
    private $passes = 2;

    public function __construct($audioCodec = 'libfdk_aac', $videoCodec = 'libx264')
    {
        $this->setAudioCodec($audioCodec);
        $this->setVideoCodec($videoCodec);
    }

    /**
     * {@inheritDoc}
     */
    public function supportBFrames()
    {
        return $this->bframesSupport;
    }

    /**
     * @param $support
     *
     * @return X264
     */
    public function setBFramesSupport($support)
    {
        $this->bframesSupport = $support;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableAudioCodecs()
    {
        return array('aac', 'libvo_aacenc', 'libmp3lame', 'libfdk_aac');
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableVideoCodecs()
    {
        return array('libx264');
    }

    /**
     * @param $passes
     *
     * @return X264
     */
    public function setPasses($passes)
    {
        $this->passes = $passes;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getPasses()
    {
        return $this->passes;
    }

    /**
     * @return int
     */
    public function getModulus()
    {
        return 2;
    }
}

//ffmpeg failed to execute command '/usr/local/bin/ffmpeg' '-y' '-i' '/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/taipei_101_fireworks_trailer.mp4' '-threads' '12' '-acodec' 'libvorbis' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k' '-force_key_frames' 'expr:gte(t,n_forced*10)' '/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/output-144.mp4'

//ffmpeg failed to execute command '/usr/local/bin/ffmpeg' '-y' '-i' '/Users/agoris/Documents/clients/clickon/streampress/web/app/plugins/streampress-transcoder/videos/taipei_101_fireworks_trailer.mp4' '-ss' '00:00:30.00' '-t' '00:00:15.00' '-threads' '12' '-acodec' 'libvorbis' '-b:v' '1000k' '-refs' '6' '-coder' '1' '-sc_threshold' '40' '-flags' '+loop' '-me_range' '16' '-subq' '7' '-i_qfactor' '0.71' '-qcomp' '0.6' '-qdiff' '4' '-trellis' '1' '-b:a' '128k' 'force_key_framessdaf' 'expr:gte(t,n_forced*10)'







