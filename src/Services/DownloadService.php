<?php

namespace Laravelir\Attachmentable\Services;

use Illuminate\Http\Request;

final class DownloadService extends Service
{
    public function __construct()
    {
        parent::__construct();
    }

    public function download($id, Request $request)
    {
        if ($file = $this->model->where('uuid', $id)->first()) {}
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

}
