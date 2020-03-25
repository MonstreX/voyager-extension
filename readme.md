# Voyager Extension

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

The package extends the original Voyager Admin Panel with some new advantages and features.

## Features

- Integration of [laravel-medialibrary](https://docs.spatie.be/laravel-medialibrary/) by Spatie
- New field: Advanced ML Image, supports Title and Alt field. 
- New field: Advanced ML Files (including images), supports Sorting and unlimited attached custom fields with different types.
- New field: Select Dropdown Tree. Dropdown selection for Tree type structures (with parent_id).
- New field: Fields Group. JSON kind group of fields inside the one model field.
- New field: Page Layout. Allows to organize layout of widgets and content on a Page. Depends on Voyager Site package. 
- New extended Browse Bread appearance and options.
- Tree view mode mode for models have parent_id field

## Package installation

Via Composer

``` bash
$ composer require monstrex/voyager-extension
```

Publish config if you need:
```
$ php artisan vendor:publish --provider="MonstreX\VoyagerExtension\VoyagerExtensionServiceProvider" --tag="config"
```

To use Image fields you need publish and migrate [laravel-medialibrary](https://docs.spatie.be/laravel-medialibrary/) resources

``` bash
$ php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
$ php artisan migrate
```

Optional you may would like to publish config [laravel-medialibrary](https://docs.spatie.be/laravel-medialibrary/) as well
``` bash
$ php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config"
```

Configure
---

#### Config file

```php
/*
| Use original edit-add.blade.php or use extended one
*/
'legacy_edit_add_bread' => false,

/*
| CLone Record parameters
| @params: enabled - if action is available
|          reset_types - A value of these bread type fields will be cleared
|          suffix_fields - The suffix '(clone)' will be added to these fields content
*/
'clone_record' => [
    'enabled' => true,
    'reset_types' => ['image', 'multiple_images','file'],
    'suffix_fields' => ['title','name','slug'],
],
/*
| You can enable or disable the custom path generator for medialibrary images
| at MonstreX\VoyagerExtension\Generators\MediaLibraryPathGenerator
*/
'use_media_path_generator' => true,

```


#### Models

To use additional images fields you should to configure your models like this:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Article extends Model implements HasMedia
{
   use HasMediaTrait;    
}

```
Also you can use any other advantages provided by [laravel-medialibrary](https://docs.spatie.be/laravel-medialibrary/) package.

Usage
---

The package provide some new type fields.

>##### Field: Advanced ML Image

The field utilize **laravel-medialibrary** package to store single image. In addition this field can hold text attributes TITLE and ALT.
  
>##### Field: Advanced ML Media Files

This field represents **laravel-medialibrary** collection with subsets of additional custom fields.  
Uses to store any media files. The collection can be sorted. Select and group removing is implemented. 
By default is implemented two fields - **Title** and **Alt**. Changing a file inside a collection element is allowed.  
You may create additional type fields using **BREAD Json Options**.

>Implemented fields types:
```js
{
    "extra_fields": {
        "content": {
            "type": "textarea",
            "title": "Description",
        },
        "code": {
            "type": "codemirror",
            "title": "HTML Widget"
        },
        "link": {
            "type": "text",
            "title": "URL"
        }        
    }
}
```
>Accepted files types template:
```json
{
  "input_accept": "image/*,.pdf,.zip,.js,.html,.doc,.xsxl"
}
```
By default "image/*" is used.


Localizations
---

To be described.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [author name][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/monstrex/testpackage.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/monstrex/testpackage.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/monstrex/testpackage/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/monstrex/testpackage
[link-downloads]: https://packagist.org/packages/monstrex/testpackage
[link-travis]: https://travis-ci.org/monstrex/testpackage
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/monstrex
[link-contributors]: ../../contributors
