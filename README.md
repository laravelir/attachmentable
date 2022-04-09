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

3. Run the command below to install package:

```
php artisan attachmentable:install
```

4. Run the command below to migrate database:

```
php artisan migrate
```

## Uses

First add `Attachmentable` trait to models that you want have attachments

```php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravelir\Attachmentable\Traits\Attachmentable;

class Post extends Model
{
    use HasFactory,
        Attachmentable;
}

```

### Methods

in controllers you have these methods:

```php

namespace App\Http\Controllers;

use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        $post = Post::find(1);

        $post->attachments // return all attachments

        
    }
}

```
