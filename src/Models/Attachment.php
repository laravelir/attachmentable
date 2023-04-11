<?php

namespace Laravelir\Attachmentable\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Miladimos\Toolkit\Traits\HasUUID;
use Miladimos\Toolkit\Traits\RouteKeyNameUUID;

class Attachment extends Model
{
    use HasUUID,
        RouteKeyNameUUID,
        SoftDeletes;

    protected $table = 'attachmentables';

    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

//        if (config('attachments.behaviors.cascade_delete')) {
//            static::deleting(function ($attachment) {
//                $attachment->deleteFile();
//            });
//        }
    }

    public function attachmentorable(): MorphTo
    {
        return $this->morphTo();
    }

    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }

}
