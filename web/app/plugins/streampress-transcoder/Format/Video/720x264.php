<?php
/**
 * The StreamPress_720x264 video format
 */
class StreamPress_720x264 extends FFMpeg\Format\Video\DefaultVideo
{
    /** @var boolean */
    private $bframesSupport = true;

    /** @var integer */
    private $passes = 2;

    public function __construct($audioCodec = 'libfdk_aac', $videoCodec = 'libx264')
    {
        $this->setAudioCodec($audioCodec);
        $this->setVideoCodec($videoCodec);
        $this->setAdditionalParameters(array('-force_key_frames', 'expr:gte(t,n_forced*10)'));

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