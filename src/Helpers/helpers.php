<?php

if (!function_exists('attachmentable_path')) {
    function attachmentable_path($path = null)
    {
        return realpath(__DIR__ . '../../../' . trim($path));
    }
}

if (!function_exists('attachmentable_asset')) {
    function attachmentable_asset($asset): string
    {
        return asset('attachmentable' . trim($asset));
    }
}

if (!function_exists('attachmentable_disk')) {
    function attachmentable_disk()
    {
        return config('attachmentable.disk');
    }
}


