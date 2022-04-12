<?php

// config file for laravelir/attachmentable
return [
    // All of Functionality in this Directory
    'base_directory' => env("ATTACHMENTABLE_BASE_DIR", 'attachments'),

    'attachment_model' => \Laravelir\Attachmentable\Models\Attachment::class,

    /**
     * en, fa
     */
    'locale' => [
        'default' => 'en',
    ],

    /**
     * List of disk names that you want to use for upload
     *
     * local, public
     *
     */
    'disk' => env('ATTACHMENTABLE_DISK', 'public'),

    /**
     * The maximum upload file size of an item in bytes.
     * Adding a larger file will result in an exception.
     */
    'max_file_size' => 1024 * 1024 * 10,
];
