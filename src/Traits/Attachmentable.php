<?php

namespace Laravelir\Attachmentable\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Laravelir\Attachmentable\Models\Attachment;
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

    public function detach(): bool
    {
        return true;
    }

    public function hasAttachment($key): bool
    {
        return true;
    }

    public function clearAttachments(): bool
    {
        return true;
    }
}
