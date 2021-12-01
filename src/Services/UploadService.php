<?php

namespace Laravelir\Attachmentable\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


final class UploadService extends Service
{
    private $defaultUploadFolderName = 'uploads';


    public $filename;

    public $file;

    public $options;


    public function __construct()
    {
        parent::__construct();
    }

    public function name($name)
    {
        $this->filename = $name;
        return $this;
    }

    public function uploadOneFile(UploadedFile $uploadedFile, $path = null)
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

    public function uploadOneImage(UploadedFile $uploadedFile, $path = null)
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

    public function path($path)
    {
        return $this->defaultUploadFolderName . $this->ds . $path;
    }

    function mkdir_if_not_exists($dirPath)
    {
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
    }

    public function deleteOne($folder = null, $filename = null, $disk = 'public')
    {
        Storage::disk($disk)->delete($folder . $filename);
    }
}
