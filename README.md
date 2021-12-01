- [![Starts](https://img.shields.io/github/stars/laravelir/attachmentable?style=flat&logo=github)](https://github.com/laravelir/attachmentable/forks)
- [![Forks](https://img.shields.io/github/forks/laravelir/attachmentable?style=flat&logo=github)](https://github.com/laravelir/attachmentable/stargazers)

# Laravel attachmentable package

A package for attachment files to models

## Installation

1. Run the command below to add this package:

```
composer require laravelir/attachmentable
```

2. Open your config/attachmentable.php and add the following to the providers array:

```php
Laravelir\Attachmentable\Providers\AttachmentableServiceProvider::class,
```

1. Run the command below to install package:

```
php artisan attachmentable:install
```
