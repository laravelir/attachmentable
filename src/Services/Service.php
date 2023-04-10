<?php

namespace Laravelir\Attachmentable\Services;

use Illuminate\Support\Facades\Storage;

abstract class Service
{
    public string $disk;

    public string $ds = DIRECTORY_SEPARATOR;

    public function __construct()
    {
        $this->setDisk(config('attachmentable.disk'));
    }

    public function setDisk(string $disk)
    {
        $this->disk = $disk;
        return $this;
    }

    public function disk(): \Illuminate\Contracts\Filesystem\Filesystem
    {
        return Storage::disk($this->disk);
    }

    /**
     * Returns true if the storage engine is local.
     *
     * @return bool
     */
    protected function isLocalStorage(): bool
    {
        return $this->disk == 'local';
    }

    /**
     * Returns true if the storage engine is public.
     *
     * @return bool
     */
    protected function isPublicStorage(): bool
    {
        return $this->disk == 'public';
    }
}
