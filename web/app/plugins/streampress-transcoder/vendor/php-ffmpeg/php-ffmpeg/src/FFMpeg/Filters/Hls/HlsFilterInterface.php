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

use FFMpeg\Filters\FilterInterface;
use FFMpeg\Media\Hls;

interface HlsFilterInterface extends FilterInterface
{
    public function apply(Hls $hls);
}
