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
        $this->disk = config('attachmentable.disk');
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

}
