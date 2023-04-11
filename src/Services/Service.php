<?php

namespace Laravelir\Attachmentable\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

abstract class Service
{
    public string $disk;

    public string $ds = DIRECTORY_SEPARATOR;

    public string $defaultUploadFolderName;

    // attachment model
    public $model;

    public function __construct()
    {
        $this->setupSetting();
    }

    public function setupSetting()
    {
        $this->setDisk(config('attachmentable.disk'));
        $this->defaultUploadFolderName = config('attachmentable.behaviors.uploads.default_directory');
        $this->model = config('attachmentable.attachment_model');
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

    protected function isLocalStorage(): bool
    {
        return $this->disk == 'local';
    }

    protected function isPublicStorage(): bool
    {
        return $this->disk == 'public';
    }
}
