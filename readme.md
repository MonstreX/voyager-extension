# Voyager Extension

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]

The package extends the original [Voyager Admin Panel](https://github.com/the-control-group/voyager) with some new advantages and features.

## Features

- Integration of [laravel-medialibrary](https://docs.spatie.be/laravel-medialibrary/) by Spatie
- New field: VE Image, supports Title and Alt field.
- New field: VE Media Files (including images), supports Sorting and unlimited attached custom fields with different types.
- New field: VE Select Dropdown Tree. Dropdown selection for Tree type structures (with parent_id).
- New field: VE Fields Group. JSON kind group of fields inside the one model field.
- New field: VE Page Layout. Allows to organize layout of widgets and content on a Page. Depends on Voyager Site package.
- New extended Browse Bread appearance and options.
- Custom Browse columns order.
- Tabs Layout for an add-edit BREAD mode.
- Tree view mode mode for models have parent_id field.

## Package installation

Via Composer

``` bash
$ composer require monstrex/voyager-extension
```

Then run migrations:

``` bash
$ php artisan migrate
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

Optional you may like to publish the config [laravel-medialibrary](https://docs.spatie.be/laravel-medialibrary/) as well
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

>### Field: VE Image

The field utilize **laravel-medialibrary** package to store single image. In addition this field can hold text attributes TITLE and ALT.

![VE Image](/docs/images/adv-image.png)

>### Field: VE Media Files

This field represents **laravel-medialibrary** collection with subsets of additional custom fields. Uses to store any media files.
The collection can be sorted as you need using drag and drop. Select and group removing is implemented.

By default it keeps two fields - **Title** and **Alt**. Changing a file inside a collection element is allowed.
You can use the field like a collection of widgets or just like a sortable image collection.
Elements of media collection can hold additional content fields using **BREAD Json Options**.


>Implemented fields types:
```json
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
By default uses "image/*" template.

![VE Media Files](/docs/images/adv-media-files.png)

> Retrieving field data on frontend side.

You can use any method provided by laravel-medialibrary package. The field name of is represented media gallery name.

```php
$image = $post->getFirstMedia('field_name');
$imageUrl = $image->getFullUrl();
$imageTitle = $image->getCustomProperty('title');
$imageAlt = $image->getCustomProperty('alt');
```
More details see in the original [laravel-medialibrary documentation](https://docs.spatie.be/laravel-medialibrary/v7/basic-usage/retrieving-media/).

>### Field: VE Fields Group

Is a simple JSON like fieldset. Support three field subtypes inside: text, number and textarea.
Useful when you need implement the same group fields in different models.
![Fields Group](/docs/images/fields-group.png)

BREAD Json Options:
```json
{
    "fields": {
        "seo_title": {
            "label": "SEO Title",
            "type": "text"
        },
        "meta_description": {
            "label": "META Description",
            "type": "text"
        },
        "meta_keywords": {
            "label": "META Keywords",
            "type": "text"
        }
    }
}
```

Retrieving data:
```blade
@if($seo = json_decode($Post->seo->fields))
  <title>{{ $seo->seo_title->value }}</title>
@endif
```
>### Field: VE Select Dropdown Tree

Represents tree-like dropdown control related to the certain model.
BREAD Json Options (Post model, category_id field):

```json
{
    "relationship": {
        "field": "category",
        "key": "id",
        "label": "title"
    }
}
```
In a Post model add:
```php
public function categoryId()
{
   return $this->belongsTo(Category::class);
}
```

>### Field: VE Page Layout

The special content field type. Available only if [Voyager Site](https://github.com/MonstreX/voyager-site) package is installed.
Provides a subsystem to organize the layout of content fields, blocks, and forms on a page.

![VE Page Layout](/docs/images/page-layout.png)

BREAD Json Option for this field:
```json
{
    "layout_fields": {
        "content": "Content"
    },
    "block_model": "MonstreX\\VoyagerSite\\Models\\Block",
    "form_model": "MonstreX\\VoyagerSite\\Models\\Form",
    "style_classes": "col-md-3"
}
```

**layout_fields** - list of model (bread) fields available for a selection.

**block_model** - block model class used for retrieve block content records.  If param is not present, the block model select input will not be displayed on the edit/add views.

**form_model** - form model class used for retrieve form content records.  If param is not present, the form model select input will not be displayed on the edit/add views;

**style_classes** - additional style classes to be applied to the select input fields on the edit/add views.  Default value: `col-md-4`.

Rendering the field:
```blade
{!! render_layout($page->layout_field, $page) !!}
```

>### New BREAD Browse modes and options

### Tree mode

New TREE browse mode implemented. If you have the field **parent_id** you can add option **browse_tree** for it and then TREE browse mode will enabled :
```json
{
    "browse_tree": true
}
```
The tree mode looks similar to the menu tree view.

![Tree mode](/docs/images/tree-view.png)

You can use option **browse_tree_push_right** to push browsed fields to the right part of the view line.
```json
{
    "browse_tree_push_right": true
}
```
All browsed fields after this field will push right.

### Alternate browse title

Just replaces default bread field title with a provided option title:

```json
{
    "browse_title": "Short title"
}
```

### Inline checkbox switcher

Using **browse_inline_checkbox** you can enable an inline switcher in a browse view mode.
After that you can change the field value directly (by clicking on it) from a browse mode without entering an edit mode.

![Inline checkbox switcher](/docs/images/inline-checkbox.png)

```json
{
    "browse_inline_checkbox": true,
    "on": "Active",
    "off": "Disabled",
    "checked": true
}
```

### Action on a field click

If you add this option *url* you will be able to call appropriate action for the record using just a click on it.
For an instance let it be field **Title**
```json
{
    "url": "edit"
}
```

### Column width, align and font size
Sets width, align and font-size for the column in browse mode:
```json
{
    "browse_width": "15px",
    "browse_align": "right",
    "browse_font_size": "0.8em"
}
```

### Column order

Now you can change the column order in a browse mode using this option:
```json
{
    "browse_order": 1
}
```

### Image max height in a row

Sets maximal height of thumbnail images:
```json
{
    "browse_image_max_height": "30px"
}
```

### Section separator

This option makes a visual section separator line.
```json
{
    "section": "Media files"
}
```

### Tabs layout for add-edit mode

In add-edit BREAD mode you can use Tabbed layout. Just put the option **tab_title** where you want to start a new TAB.

![Inline checkbox switcher](/docs/images/tabs.png)

```json
{
    "tab_title": "Media"
}
```
You don't need to make the first TAB, it'll be created automatically.

Localizations
---

New types of fields don't provide localization service used in Voyager.
But you can use built-in localization helper and retrieve translated substring from a field content:

```php
$field_data = '{{en}}English title {{ru}}Russian title';
...
$field_title_en = str_trans($field_data);
$field_title_ru = str_trans($field_data,'ru');
```

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
