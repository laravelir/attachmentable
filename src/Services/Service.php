<?php

namespace Laravelir\Attachmentable\Services;

use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

abstract class Service
{
    public string $disk;

    public string $ds = DIRECTORY_SEPARATOR;

    public string $base_directory;


    // attachment model
    public $model;

    public function __construct()
    {
        $this->setupSetting();
    }

    public function setupSetting()
    {
        $this->setDisk(config('attachmentable.disk'));
        $this->base_directory = config('attachmentable.base_directory');
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
     * @param $path
     * @return string
     * result =
     */
    public function generatePath($path): string
    {

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $day = Carbon::now()->day;

        if ($this->isLocalStorage()) {
            $st_path = storage_path('app');
        } else if ($this->isPublicStorage()) {
            $st_path = storage_path() . $this->ds . 'app' . $this->ds . 'public';
        }

        return "{$st_path}{$this->ds}{$this->base_directory}{$this->ds}{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";
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

    public function setFileMetadata($file): array
    {
//        $fileName = $file->getFilename();
//        $originalFileName = $file->getClientOriginalName();

        $data = [
            'ext' => $file->getClientOriginalExtension(),
            'mime' => $file->getClientMimeType(),
            'size' => method_exists($file, 'getSize') ? $file->getSize() : $file->getClientSize(),

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
