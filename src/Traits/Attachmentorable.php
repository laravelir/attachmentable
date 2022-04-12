<?php

namespace Laravelir\Attachmentable\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Laravelir\Attachmentable\Models\Attachment;
use Laravelir\Attachmentable\Services\AttachmentService;

trait Attachmentorable
{
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachmentorable');
    }

    public function attachment(string $key)
    {
        return $this->attachments->where('key', $key)->first();
    }

    public function attach(Model $model, $file, array $option = null): bool
    {
        if (!$model instanceof Model) {
            return false;
        }

        $model->attachments()->create([
            'attachmentorable_id' => $this->id,
            'attachmentorable_type' => get_class($this),

        ]);

        return true;
    }

    public function delete()
    {
        # code
    }
}
