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

    public function attach($file, $path = null): bool
    {
        $attachmentService = resolve(AttachmentService::class);

//        if (empty($fileOrPath)) {
//            throw new \Exception('Attached file is required');
//        }
//
        if ($file instanceof UploadedFile)
        {
            if(! $attachmentService->attach($file, $this)) return false;
            return true;
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


}
