<?php

namespace Laravelir\Attachmentable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Lang;
use Laravelir\Attachmentable\Models\Attachment;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Laravelir\Attachmentable\Services\AttachmentService;

trait Attachmentable
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentable', 'attachmentables');
    }

    public function attachment(string $key)
    {
        return $this->attachments->where('key', $key)->first();
    }

    public function attach($file, array $options = null): bool
    {
        $attachmentService = resolve(AttachmentService::class);

        if ($file instanceof UploadedFile)
        {
            $attachmentService->attach($file, $this);
        }

        return false;
    }

    public function hasAttachment($key)
    {
        # code...
    }

    public function clearAttachments($group)
    {
        # code...
    }

    public function attachFromUrl($url, array $options = null)
    {
        # code...
    }

    public static function attach3($uuid, $model, $options = [])
    {
        /** @var Attachment $attachment */
        $attachment = self::where('uuid', $uuid)->first();

        if (!$attachment) {
            return null;
        }

        // The dz_session_key is set by the build-in DropzoneController for security check
        if ($attachment->metadata('dz_session_key')) {
            $meta = $attachment->metadata;

            unset($meta['dz_session_key']);

            $attachment->metadata = $meta;
        }

        $options = Arr::only($options, config('attachments.attributes'));

        $attachment->fill($options);

        if ($found = $model->attachments()->where('key', '=', $attachment->key)->first()) {
            $found->delete();
        }

        return $attachment->model()->associate($model)->save() ? $attachment : null;
    }

    public function deleteOne()
    {
        # code
    }

    public function delete()
    {
        # code
    }

    public function download($id, Request $request)
    {
        $disposition = ($disposition = $request->input('disposition')) === 'inline' ? $disposition : 'attachment';

        if ($file = $this->model->where('uuid', $id)->first()) {
            try {
                /** @var \Bnb\Laravel\Attachments\Attachment $file */
                if (!$file->output($disposition)) {
                    abort(403, Lang::get('attachments::messages.errors.access_denied'));
                }
            } catch (FileNotFoundException $e) {
                abort(404, Lang::get('attachments::messages.errors.file_not_found'));
            }
        }

        abort(404, Lang::get('attachments::messages.errors.file_not_found'));
    }

    public function attach2($fileOrPath, $options = [])
    {
        if (!is_array($options)) {
            throw new \Exception('Attachment options must be an array');
        }

        if (empty($fileOrPath)) {
            throw new \Exception('Attached file is required');
        }

        $attributes = Arr::only($options, config('attachments.attributes'));

        if (!empty($attributes['key']) && $attachment = $this->attachments()->where('key', $attributes['key'])->first()) {
            $attachment->delete();
        }

        /** @var Attachment $attachment */
        $attachment = app(AttachmentContract::class)->fill($attributes);
        $attachment->filepath = !empty($attributes['filepath']) ? $attributes['filepath'] : null;

        if (is_resource($fileOrPath)) {
            if (empty($options['filename'])) {
                throw new \Exception('resources required options["filename"] to be set?');
            }

            $attachment->fromStream($fileOrPath, $options['filename']);
        } elseif ($fileOrPath instanceof UploadedFile) {
            $attachment->fromPost($fileOrPath);
        } else {
            $attachment->fromFile($fileOrPath);
        }

        if ($attachment = $this->attachments()->save($attachment)) {
            return $attachment;
        }

        return null;
    }
}
