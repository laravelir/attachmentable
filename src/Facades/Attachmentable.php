<?php

namespace Laravelir\Attachmentable\Facades;

use Illuminate\Support\Facades\Facade;

class Attachmentable extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'attachmentable';
    }
}
