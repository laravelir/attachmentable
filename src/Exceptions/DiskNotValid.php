<?php

namespace Laravelir\Attachmentable\Exceptions;

final class CanNotAttachFile extends BaseException
{
    public static function create(string $diskName): self
    {
        return new static("There is no filesystem disk named `{$diskName}`");
    }
}
