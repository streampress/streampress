<?php

/*
 * This file is part of PHP-FFmpeg.
 *
 * (c) Alchemy <dev.team@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FFMpeg\Filters\Hls;

use FFMpeg\Media\Hls;

class HlsFilters
{
    private $hls;

    public function __construct(Hls $hls)
    {
        $this->hls = $hls;
    }

    // add a key
    public function keyInfoFile()
    {
        $this->hls->addFilter("hello");

        return $this;
    }

}
