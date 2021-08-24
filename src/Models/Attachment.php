<?php

namespace Laravelir\Attachmentable\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attachmentables';

    // protected $fillable = ['name'];

    protected $guarded = [];
}
