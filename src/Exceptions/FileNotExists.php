<?php

namespace Laravelir\Attachmentable\Exceptions;

final class FileNotExists extends BaseException
{
    public static function create(string $path): self
    {
        return new static("File `{$path}` does not exist");
    }
}
