<?php

if (!function_exists('attachmentable_path')) {
    function attachmentable_path($path = null)
    {
        return realpath(__DIR__ . '../../../' . trim($path));
    }
}

if (!function_exists('attachmentable_asset')) {
    function attachmentable_asset($asset)
    {
        return asset('attachmentable' . trim($asset));
    }
}

if (!function_exists('attachmentable_locale')) {
    function attachmentable_locale()
    {
        return config('attachmentable.locales.default');
    }
}

if (!function_exists('attachmentable_lang')) {
    function attachmentable_lang($key)
    {
        switch (config('attachmentable.locales.default')) {
            case 'en':
                return trans('attachmentable::messages.' . $key, [], 'en');
                break;
            case 'fa':
                return trans('attachmentable::messages.' . $key, [], 'fa');
                break;
            default:
                return trans('attachmentable::messages.' . $key, [], 'en');
                break;
        }
    }
}
