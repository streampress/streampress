<?php
class CustomVideo extends FFMpeg\Format\Video\DefaultVideo
{
    /** @var boolean */
    private $bframesSupport = true;

    public function __construct($audioCodec = 'libmp3lame', $videoCodec = 'libx264')
    {
        $this
            ->setAudioCodec($audioCodec)
            ->setVideoCodec($videoCodec);
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
        return array('libmp3lame');
    }

    /**
     * {@inheritDoc}
     */
    public function getAvailableVideoCodecs()
    {
        return array('libx264');
    }

    /**
     * {@inheritDoc}
     */
    public function getPasses()
    {
        return 2;
    }

    /**
     * @return int
     */
    public function getModulus()
    {
        return 2;
    }
}