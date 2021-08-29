<?php

namespace Laravelir\Attachmentable\Traits;

trait RouteKeyNameUUID
{
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
