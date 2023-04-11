<?php

namespace Laravelir\Attachmentable\Contracts;

interface AttachmentableContract
{
    public function attach();
    public function detach();
}
