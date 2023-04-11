<?php

use Illuminate\Support\Facades\Storage;

// config file for laravelir/attachmentable
return [

    /**
     * use local - public
     */
    'disk' => env('ATTACHMENTABLE_DISK', Storage::getDefaultDriver()),


    // All Functionality in this Directory
    'base_directory' => env("ATTACHMENTABLE_BASE_DIR", 'attachments'),

    'attachment_model' => \Laravelir\Attachmentable\Models\Attachment::class,

    /**
     * The maximum upload file size of an item in bytes.
     * Adding a larger file will result in an exception.
     */
    'max_file_size' => 1024 * 1024 * 10,

    'behaviors' => [
        // if false use soft delete and if true use hard delete files
        'cascade_delete' => true,

        'uploads' => [

            /**
             * url : url($path)
             *
             */
            'uploaded_file_path' => '',
            'image' => [
                'thumbnail' => [
                    'width' => '120',
                    'height' => '120'
                ],
                'small' => '',
                'medium' => '',
                'original' => '',
            ]
        ],

        'downloads' => [],
    ],

];
