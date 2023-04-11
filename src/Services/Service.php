<?php

namespace Laravelir\Attachmentable\Services;

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

    /**
     * return upload path.
     *
     * @return string
     */
    protected function path()
    {
        return storage_path() . '/app';
    }

    public function path2($path)
    {
        return $this->defaultUploadFolderName . $this->ds . $path;
    }

    protected function isLocalStorage(): bool
    {
        return $this->disk == 'local';
    }

    protected function isPublicStorage(): bool
    {
        return $this->disk == 'public';
    }

    public function getFileExtension($file)
    {
        return pathinfo($file->original_name, PATHINFO_EXTENSION);
    }

    public function getFilePath($file)
    {
        return pathinfo($file->filepath, PATHINFO_DIRNAME);
    }

    public function getFileUrl($file)
    {
        if ($this->isLocalStorage()) {
            return $this->proxy_url;
        } else {
            return $this->disk()->url($file->filepath);
        }
    }


    public function setFileMetadata($file)
    {
        $data = [
            'ext' => $file->getClientOriginalExtension(),
        ];

        return $data;
    }

    public function getFileMetadata($key, $default = null)
    {
        if (is_null($key)) {
            return $this->metadata;
        }

        return Arr::get($this->metadata, $key, $default);
    }

    /**
     * Generate a temporary url at which the current file can be downloaded until $expire
     *
     * @param Carbon $expire
     * @param bool $inline
     *
     * @return string
     */
    public function getTemporaryUrl(Carbon $expire, $inline = false)
    {

        $payload = Crypt::encryptString(collect([
            'id' => $this->uuid,
            'expire' => $expire->getTimestamp(),
            'shared_at' => Carbon::now()->getTimestamp(),
            'disposition' => $inline ? 'inline' : 'attachment',
        ])->toJson());

        return route('attachments.download-shared', ['token' => $payload]);

    }

    protected function isDirectoryEmpty($dir)
    {
        if (!$dir || !$this->storageCommand('exists', $dir)) {
            return null;
        }

        return count($this->storageCommand('allFiles', $dir)) === 0;
    }

    protected function copyToStorage($localPath, $storagePath)
    {
        return Storage::disk($this->disk)->put($storagePath, FileHelper::get($localPath));
    }

    protected function deleteEmptyDirectory($dir = null)
    {
        if (!$this->isDirectoryEmpty($dir)) {
            return;
        }

        $this->storageCommand('deleteDirectory', $dir);

        $dir = dirname($dir);

        if (!$this->isDirectoryEmpty($dir)) {
            return;
        }

        $this->storageCommand('deleteDirectory', $dir);

        $dir = dirname($dir);

        if (!$this->isDirectoryEmpty($dir)) {
            return;
        }

        $this->storageCommand('deleteDirectory', $dir);
    }


}
