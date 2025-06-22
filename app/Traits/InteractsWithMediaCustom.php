<?php

namespace App\Traits;

use Spatie\MediaLibrary\InteractsWithMedia;

trait InteractsWithMediaCustom
{
    use InteractsWithMedia;

    public function getFirstMediaUrlCustom(string $name)
    {
        $url = $this->getFirstMediaUrl($name);
        return !empty($url) ? $url : null;
    }
}