<?php

namespace Laravelir\Attachmentable\Exceptions;

final class DiskNotValid extends BaseException
{
    public static function create(string $diskName): self
    {
        return new static("The filesystem disk not valid `{$diskName}`");
    }
}
