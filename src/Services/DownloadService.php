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
        $disposition = ($disposition = $request->input('disposition')) === 'inline' ? $disposition : 'attachment';

        if ($file = $this->model->where('uuid', $id)->first()) {
            try {
                if (!$file->output($disposition)) {
                    abort(403, Lang::get('attachments::messages.errors.access_denied'));
                }
            } catch (FileNotFoundException $e) {
                abort(404, Lang::get('attachments::messages.errors.file_not_found'));
            }
        }

        abort(404, Lang::get('attachments::messages.errors.file_not_found'));
    }


}
