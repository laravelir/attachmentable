<?php

namespace Laravelir\Attachmentable\Services;

use Carbon\Carbon;
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
        if (!$file instanceof UploadedFile) {
            return false;
        }

        // $this->disk()->put();

        $attachmentable->attachments()->create([
            'attachmentorable_id' => auth()->user()->id,
            'attachmentorable_type' => get_class(auth()->user()),
        ]);


    }

    public function detach($file)
    {
        $this->disk()->delete($file);
    }

    public function uploadFile(UploadedFile $uploadedFile, $path = null)
    {

        $path = $this->path($path);

        if ($uploadedFile->isValid()) {
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

                $uploadedFile->move($dirPath, $finalFileName);


                return $uploadPath . $this->ds . $finalFileName;
            }

            $uploadedFile->move($dirPath, $fileName);

            return $uploadPath . $this->ds . $fileName;
        }

        return false;
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

    public function fromPost($file, $disk = null)
    {
        if ($file === null) {
            return null;
        }

        $this->filename = $file->getClientOriginalName();
        $this->filesize = method_exists($file, 'getSize') ? $file->getSize() : $file->getClientSize();
        $this->filetype = $file->getMimeType();
        $this->filepath = $this->filepath ?: ($this->getStorageDirectory() . $this->getPartitionDirectory() . $this->getDiskName());
        $this->filename = $file->getFilename();
        $this->filesize = $file->getSize();
        $this->filetype = $file->getMimeType();
        $this->filepath = $this->filepath ?: ($this->getStorageDirectory() . $this->getPartitionDirectory() . $this->getDiskName());
        $this->putFile($file->getRealPath(), $this->filepath);

        $this->putFile($file->getRealPath(), $this->filepath);

        return $this;
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

    protected function putFile($sourcePath, $filePath = null)
    {
        if ( ! $filePath) {
            $filePath = $this->filepath;
        }

        if ( ! $this->isLocalStorage()) {
            return $this->copyToStorage($sourcePath, $filePath);
        }

        $destinationPath = $this->getLocalRootPath() . '/' . pathinfo($filePath, PATHINFO_DIRNAME) . '/';

        if (
            ! FileHelper::isDirectory($destinationPath) &&
            ! FileHelper::makeDirectory($destinationPath, 0777, true, true) &&
            ! FileHelper::isDirectory($destinationPath)
        ) {
            trigger_error(error_get_last()['message'], E_USER_WARNING);
        }

        return FileHelper::copy($sourcePath, $destinationPath . basename($filePath));
    }

}
