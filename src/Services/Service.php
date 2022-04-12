<?php

namespace Laravelir\Attachmentable\Services;

use Illuminate\Support\Facades\Storage;

abstract class Service
{
    public $disk;

    public $ds = DIRECTORY_SEPARATOR;

    /**
     * Image Sizes
     *
     * @var string
     */
    private $sizes = [
        'thumbnail' => [
            'width' => '120',
            'height' => '120'
        ],
        'small' => '',
        'medium' => '',
        'original' => '',
    ];

    public function __construct()
    {
        $this->setDisk(config('attachmentable.disk'));
    }

    public function setDisk(string $disk)
    {
        $this->disk = $disk;
        return $this;
    }

    public function disk()
    {
        return Storage::disk($this->disk);
    }

    /**
     * Returns true if the storage engine is local.
     *
     * @return bool
     */
    protected function isLocalStorage()
    {
        return $this->disk == 'local';
    }

    /**
     * Returns true if the storage engine is public.
     *
     * @return bool
     */
    protected function isPublicStorage()
    {
        return $this->disk == 'public';
    }
}
