<?php

namespace Laravelir\Attachmentable\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Laravelir\Attachmentable\Models\Attachment;

trait Attachmentorable
{
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachmentorable', 'attachmentables');
    }

    public function attach(Model $model, UploadedFile $file, array $option = null): bool
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

    public function detach($id)
    {
        # code
    }
}
