<?php

namespace Laravelir\Attachmentable\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


final class AttachmentService extends Service
{
    public string $filename;

    public $file;

    public $options;

    public function __construct()
    {
        parent::__construct();
    }

    public function attach($file, $attachmentable, $path)
    {
        if (!$file instanceof UploadedFile && !$file->isValid()) {
            return false;
        }

        if (!$attachmentable instanceof Model) {
            return false;
        }

        $uploadedFilePath = $this->upload($file, $path);

        if ($uploadedFilePath == false) {
            return false;
        }

        $created = $attachmentable->attachments()->create([
            'attachmentorable_id' => auth()->user()->id,
            'attachmentorable_type' => get_class(auth()->user()),
            'attachmentable_id' => $attachmentable->id,
            'attachmentable_type' => get_class($attachmentable),
            'path' => $uploadedFilePath,
            'disk' => $this->disk,
            'meta' => $this->setFileMetadata($file),
        ]);

        if (!$created) {
            return false;
        }

        return true;
    }

    public function detach($file)
    {
        $this->disk()->delete($file);
    }

    public function upload($file, $path)
    {
        $destinationPath = $this->generatePath($path);

        $fileName = now()->timestamp . '-' . $file->getClientOriginalName();

        //        $this->mkdir_if_not_exists($destinationPath);

        //$this->disk()->put();
        $this->disk()->putFileAs($destinationPath, $file, $fileName);

        return $destinationPath . $this->ds . $fileName;
    }

    function mkdir_if_not_exists($dirPath)
    {
        if (!$this->disk()->exists($dirPath)) {
            $this->disk()->makeDirectory($dirPath);
        }
    }

    public function uploadImage(UploadedFile $uploadedFile, $path = null)
    {
        //      $image = Image::make($uploadedFile->getRealPath());
    }

    public function isFile($file): bool
    {
        return true;
    }

    public function isImage($file): bool
    {
        return true;
    }

    public function attachFromUrl($url, array $options = null)
    {
        # code...
    }

    public function attachFromStream($stream, $filename, $disk = null)
    {
        if ($stream === null) {
            return null;
        }

        $this->disk = $this->disk ?: ($disk ?: Storage::getDefaultDriver());

        $driver = Storage::disk($this->disk);

        $this->filename = $filename;
        $this->filepath = $this->filepath ?: ($this->getStorageDirectory() . $this->getPartitionDirectory() . $this->getDiskName());

        $driver->putStream($this->filepath, $stream);

        $this->filesize = $driver->size($this->filepath);
        $this->filetype = $driver->mimeType($this->filepath);

        return $this;
    }
}
