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

    public function attachment($id)
    {
        return $this->attachments()->find($id)->first();
    }

    public function attach($file, $path): bool
    {
        if (empty($path)) {
            throw new \Exception('path for attach file is required');
        }

        $attachmentService = resolve(AttachmentService::class);

        if (!$attachmentService->attach($file, $this, $path)) return false;

        return true;

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
