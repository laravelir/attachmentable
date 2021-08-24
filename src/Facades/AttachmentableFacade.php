<?php

namespace Laravelir\Attachmentable\Facades;

use Illuminate\Support\Facades\Facade;

class AttachmentableFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'attachmentable'; // TODO: Change the accessor name
    }
}
