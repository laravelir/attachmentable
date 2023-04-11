<?php

namespace Laravelir\Attachmentable\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
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

    public function attach($file, $attachmentable)
    {
        if (!$file instanceof UploadedFile && $file->isValid()) {
            return false;
        }

        if (!$attachmentable instanceof Model) {
            return false;
        }

        $uploadedFilePath = $this->upload($file);

        if ($uploadedFilePath == false) {
            return false;
        }

        $attachmentable->attachments()->create([
            'attachmentorable_id' => auth()->user()->id,
            'attachmentorable_type' => get_class(auth()->user()),
            'attachmentable_id' => $attachmentable->id,
            'attachmentable_type' => get_class($attachmentable),
            'path' => $uploadedFilePath,
            'disk' => $this->disk,
        ]);


    }

    public function detach($file)
    {
        $this->disk()->delete($file);
    }

    public function upload(UploadedFile $file, $path = null)
    {
        $path = $this->path($path);

        $year = Carbon::now()->year;
        $month = Carbon::now()->month;
        $day = Carbon::now()->day;

        $fileName = $file->getFilename();
        $originalFileName = $file->getClientOriginalName();
        $fileExt = $file->getClientOriginalExtension();
        $mimeType = $file->getClientMimeType();
        $fileSize = method_exists($file, 'getSize') ? $file->getSize() : $file->getClientSize();

        $uploadPath = "{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";

        $fullUploadedPath = public_path($uploadPath . $this->ds . $fileName);

        $dirPath = public_path($uploadPath);

        $this->mkdir_if_not_exists($dirPath);

        if (file_exists($fullUploadedPath)) {
            $finalFileName = Carbon::now()->timestamp . "-{$fileName}";

            $file->move($dirPath, $finalFileName);


            return $uploadPath . $this->ds . $finalFileName;
        }

        $file->move($dirPath, $fileName);

        return $uploadPath . $this->ds . $fileName;
    }

    public function uploadImage(UploadedFile $uploadedFile, $path = null)
    {

        $path = $this->path($path);

        if ($uploadedFile->isValid()) {

            $image = Image::make($uploadedFile->getRealPath());
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
            $day = Carbon::now()->day;

            $fileName = $uploadedFile->getClientOriginalName();
            $fileExt = $uploadedFile->getClientOriginalExtension();
            $mimeType = $uploadedFile->getClientMimeType();
            $fileSize = $uploadedFile->getSize();

            $uploadPath = "{$path}{$this->ds}{$year}{$this->ds}{$month}{$this->ds}{$day}";

            $fullUploadedPath = public_path($uploadPath . $this->ds . $fileName);

            $dirPath = public_path($uploadPath);

            $this->mkdir_if_not_exists($dirPath);

            if (file_exists($fullUploadedPath)) {
                $finalFileName = Carbon::now()->timestamp . "-{$fileName}";

                $image->save($dirPath . $this->ds . $finalFileName);

                return $uploadPath . $this->ds . $finalFileName;
            }

            $image->save($fullUploadedPath);

            return $uploadPath . $this->ds . $fileName;
        }

        return false;
    }

    function mkdir_if_not_exists($dirPath)
    {
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
    }

    public function output($disposition = 'inline')
    {

        header("Content-type: " . $this->filetype);
        header('Content-Disposition: ' . $disposition . '; filename="' . $this->filename . '"');
        header('Cache-Control: private');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $this->filesize);

        exit($this->getContents());
    }

    public function isFile($file): bool
    {
        return true;
    }

    public function isImage($file): bool
    {
        return true;
    }


}
