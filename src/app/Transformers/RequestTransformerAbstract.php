<?php

namespace App\Transformers;

use App\Helpers\Version;
use League\Fractal\TransformerAbstract;

abstract class RequestTransformerAbstract extends TransformerAbstract
{
    /**
     * @param string $version
     * @return bool
     */
    protected function isVersionBefore(string $version): bool
    {
        return Version::isOlderThan($version);
    }
}
