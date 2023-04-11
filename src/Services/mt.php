<?php




/**
 * Creates a file object from a stream
 *
 * @param resource $stream   source stream
 * @param string   $filename the resource filename
 * @param string   $disk     target storage disk
 *
 * @return $this|null
 */
public function fromStream($stream, $filename, $disk = null)
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
