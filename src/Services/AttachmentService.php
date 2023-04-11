<?php

namespace Laravelir\Attachmentable\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;


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



    /**
     * Creates a file object from a file an uploaded file.
     *
     * @param UploadedFile $uploadedFile source file
     * @param string       $disk         target storage disk
     *
     * @return $this|null
     */
    public function fromPost($uploadedFile, $disk = null)
    {
        if ($uploadedFile === null) {
            return null;
        }

        $this->filename = $uploadedFile->getClientOriginalName();
        $this->filesize = method_exists($uploadedFile, 'getSize') ? $uploadedFile->getSize() : $uploadedFile->getClientSize();
        $this->filetype = $uploadedFile->getMimeType();
        $this->filepath = $this->filepath ?: ($this->getStorageDirectory() . $this->getPartitionDirectory() . $this->getDiskName());
        $this->filename = $file->getFilename();
        $this->filesize = $file->getSize();
        $this->filetype = $file->getMimeType();
        $this->filepath = $this->filepath ?: ($this->getStorageDirectory() . $this->getPartitionDirectory() . $this->getDiskName());
        $this->putFile($file->getRealPath(), $this->filepath);

        $this->putFile($uploadedFile->getRealPath(), $this->filepath);

        return $this;
    }



    /**
     * Register an outputting model event with the dispatcher.
     *
     * @param \Closure|string $callback
     *
     * @return void
     */
    public static function outputting($callback)
    {
        static::registerModelEvent('outputting', $callback);
    }

    public function output($disposition = 'inline')
    {
        if ($this->fireModelEvent('outputting') === false) {
            return false;
        }

        header("Content-type: " . $this->filetype);
        header('Content-Disposition: ' . $disposition . '; filename="' . $this->filename . '"');
        header('Cache-Control: private');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Cache-Control: pre-check=0, post-check=0, max-age=0');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . $this->filesize);

        exit($this->getContents());
    }


    /**
     * Get file contents from storage device.
     */
    public function getContents()
    {
        return $this->storageCommand('get', $this->filepath);
    }


    /**
     * Get a metadata value by key with dot notation
     *
     * @param string $key     The metadata key, supports dot notation
     * @param mixed  $default The default value to return if key is not found
     *
     * @return array|mixed
     */
    public function metadata($key, $default = null)
    {
        if (is_null($key)) {
            return $this->metadata;
        }

        return Arr::get($this->metadata, $key, $default);
    }


    /**
     * Saves a file
     *
     * @param string $sourcePath An absolute local path to a file name to read from.
     * @param string $filePath   A storage file path to save to.
     *
     * @return bool
     */
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


    protected function deleteFile()
    {
        $this->storageCommand('delete', $this->filepath);
        $this->deleteEmptyDirectory($this->path);
    }


    /**
     * Generates a disk name from the supplied file name.
     */
    protected function getDiskName()
    {
        if ($this->filepath !== null) {
            return $this->filepath;
        }

        $ext = strtolower($this->getExtension());
        $name = str_replace('.', '', $this->uuid);

        return $this->filepath = $ext !== null ? $name . '.' . $ext : $name;
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


    /**
     * Generates a partition for the file.
     * return /ABC/DE1/234 for an name of ABCDE1234.
     *
     * @return mixed
     */
    protected function getPartitionDirectory()
    {
        return implode('/', array_slice(str_split($this->uuid, 3), 0, 3)) . '/';
    }


    /**
     * Define the internal storage path, override this method to define.
     */
    protected function getStorageDirectory()
    {
        return config('attachments.storage_directory.prefix', 'attachments') . '/';
    }


    /**
     * Returns true if a directory contains no files.
     *
     * @param string|null $dir the directory path
     *
     * @return bool
     */
    protected function isDirectoryEmpty($dir)
    {
        if ( ! $dir || ! $this->storageCommand('exists', $dir)) {
            return null;
        }

        return count($this->storageCommand('allFiles', $dir)) === 0;
    }


    /**
     * Copy the local file to Storage
     *
     * @param string $localPath
     * @param string $storagePath
     *
     * @return bool
     */
    protected function copyToStorage($localPath, $storagePath)
    {
        return Storage::disk($this->disk)->put($storagePath, FileHelper::get($localPath));
    }


    /**
     * Checks if directory is empty then deletes it,
     * three levels up to match the partition directory.
     *
     * @param string|null $dir the directory path
     *
     * @return void
     */
    protected function deleteEmptyDirectory($dir = null)
    {
        if ( ! $this->isDirectoryEmpty($dir)) {
            return;
        }

        $this->storageCommand('deleteDirectory', $dir);

        $dir = dirname($dir);

        if ( ! $this->isDirectoryEmpty($dir)) {
            return;
        }

        $this->storageCommand('deleteDirectory', $dir);

        $dir = dirname($dir);

        if ( ! $this->isDirectoryEmpty($dir)) {
            return;
        }

        $this->storageCommand('deleteDirectory', $dir);
    }


    /**
     * Calls a method against File or Storage depending on local storage.
     * This allows local storage outside the storage/app folder and is
     * also good for performance. For local storage, *every* argument
     * is prefixed with the local root path.
     *
     * @param string $string   the command string
     * @param string $filepath the path on storage
     *
     * @return mixed
     */
    protected function storageCommand($string, $filepath)
    {
        $args = func_get_args();
        $command = array_shift($args);

        if ($this->isLocalStorage()) {
            $interface = 'File';
            $path = $this->getLocalRootPath();
            $args = array_map(function ($value) use ($path) {
                return $path . '/' . $value;
            }, $args);
        } else {
            if (substr($filepath, 0, 1) !== '/') {
                $args[0] = $filepath = '/' . $filepath;
            }

            $interface = Storage::disk($this->disk);
        }

        return forward_static_call_array([$interface, $command], $args);
    }

}
