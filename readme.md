# Voyager Extension

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

The package extends the original Voyager Admin Panel with some new advantages and features.

## Features

- Integration of laravel-medialibrary by Spatie
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

To use Image fields you need publish and migrate medialibrary resources

``` bash
$ php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
$ php artisan migrate
```

Optional you may would like to publish config medialibrary as well
``` bash
$ php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config"
```

## Configure

To use additional images fields you should to configure your models like this:

```
namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Article extends Model implements HasMedia
{
   use HasMediaTrait;    
}

```
Also you can use any other advantages of medialibrary packge.




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
