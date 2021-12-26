# Voyager Extension
The package extends the original [Voyager Admin Panel](https://github.com/the-control-group/voyager) with some new advantages and features.

## Features

- Integration of v9 [laravel-medialibrary](https://docs.spatie.be/laravel-medialibrary/) by Spatie
- Basic action buttons in add-edit mode (Save) can be Sticky and Autohide now.  
- Custom Browse columns order. Can be defined in two ways.
- Tabs Layout for an add-edit bread mode.
- Tree view mode for models have parent_id field.
- New extended Browse Bread appearance and options.
- New extended BREAD Builder appearance and options.
- New basic actions for add-edit mode: Save and continue & Save and create.

New custom fields:   
- VE Image. Title and Alt field are supported.
- VE Media Files (including images), supports Sorting and unlimited attached custom fields with different types.
- VE Select Dropdown Tree. Dropdown selection for Tree type structures (with parent_id).
- VE Fields Group. JSON kind group of fields inside the one model field.
- VE Sortable JSON Multi Fields. JSON kind group of multi-fields (multi-rows) stored in the model field.
- VE Related Models. Set of a related models list.
- VE Inline Fields Set. The complex combined field. Each field can store multiple rows (or only one) each of them may hold multiple type custom fields.
- VE Page Layout. Allows organizing layout of blocks, forms, widgets and content on a Page. Depends on Voyager Site package.

## Package installation

> #### Requirement
> You should fully install the package [Voyager](https://github.com/the-control-group/voyager) before.

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
| Use original tools/bread/edit-add.blade.php or use extended one
*/
'legacy_bread_list' => false,

/*
| Use sticky action panel (if 'enabled' = true) instead the original action buttons.
| If 'autohide' = true will show and hide automatically on mouse over/leave events.
| Uses in edit-add.blade.php and tools/bread/edit-add.blade.php
*/
'sticky_action_panel' => [
    'enabled' => true,
    'autohide' => false,
],

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
| You can enable or disable the custom path and urls generator for medialibrary images
| at MonstreX\VoyagerExtension\Generators\MediaLibraryPathGenerator
| and at MonstreX\VoyagerExtension\Generators\MediaLibraryUrlGenerator
*/
'use_media_path_generator' => true,
'use_media_url_generator' => true,

/*
|
| Use Str::slug function on media file names before saving
|
*/
'slug_filenames' => true,

```

> #### Using coordinates field
Some of legacy voyager fields like 'coordinates' don't work properly in the mode 'legacy_edit_add_bread' => false  
To make it work as supposed to be you need copy the legacy template file into your own template folder: /resources/views/vendor/voyager/formfields/coordinates.blade.php  
Then you should replace string:
```php
var gMapVm = new Vue({ el: '#coordinates-formfield' });
```
with: 
```php
if (typeof gMapVm === 'undefined') {
    var gMapVm = new Vue({ el: '#coordinates-formfield' });
}
```

#### Models

To use additional images fields you should to configure your models like this:

```php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Article extends Model implements HasMedia
{
   use InteractsWithMedia;
}

```
Also you can use any other advantages provided by [laravel-medialibrary](https://docs.spatie.be/laravel-medialibrary/) package.

Description and usage
---

Design of the BREAD Builder was changed to make it more compact and handy (can be disabled by config):

![BREAD Builder](/docs/images/bread-layout.png)

All JSON fields are collapsable now and collapsed by default. To expand json editors - just click on it.

Common extra details in BREAD Builder page:

![BREAD Extra Options](/docs/images/bread-extra.png)

New basic action buttons on the sticky panel for add-edit modes (can be disabled by config):

![Sticky Action Panel](/docs/images/sticky-panel.png)

The package also provide some new type fields.

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

>### Field: VE JSON Fields

Sortable multi-rows and multi-fields JSON storage.  
![Fields Group](/docs/images/fields-json-multi.png)

BREAD Json multi-field configuration:
```json
{
    "json_fields": {
        "group": "Param Group",
        "name": "Param Name",
        "value": "Param Value"
    }
}
```

Stored data structure (2 rows):
```json
{
  "fields": {
    "group": "Field Group",
    "name": "Field Name",
    "value": "Field value"    
  },
  "rows": [
      {
        "group":"Main",
        "name":"Services",
        "value":"12"
      },  
      {
        "group":"Second",
        "name":"Packages",
        "value": "100"
      }
  ]
}
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

>### Field: VE Related Models

Represents of a sortable model records list. Uses autocomplete field to add new specified entries.  
The model describes in details Bread field.   

![Fields Group](/docs/images/adv-related.png)

```json
{
    "related_model": {
        "source": "pages",
        "search_field": "title",
        "display_field": "title",
        "fields": [
            "title",
            "slug",
            "price"
        ]
    }
}
```
Where:  
**source:** a slug of the model  
**search_field:** a field for search  
**display_field:** a field to display as label/title,  
**fields:** set of fields to store.  

Predefined fields are: id and field mentioned in the display_field option.

Stored JSON format:
```json
[
    {
        "display_field":"title",
        "fields": {
            "id":1,
            "title":"Proin volutpat, eros sed semper hendrerit",
            "slug":"proin-volutpat-eros-sed-semper-hendrerit",
            "price": 500
        }
    },
    ...
]
```

>### Field: VE Inline Fields Set

The complex combined field. Represent groups of built in internal custom fields. 10 Internal field types supported.
Fields data can be stored as in your current model field and also as a specified table data.  

![Inline Fields Set](/docs/images/adv-inline-set.png)

Row details data for the field:
```json
{
    "inline_set": {
        "source": "App\\Models\\Meta",
        "many": true,
        "columns": 2,
        "fields": {
            "date": {
                "label": "Date",
                "type": "date"
            },
            "select": {
                "label": "Select",
                "type": "select",
                "options": {
                    "val1": "Option One",
                    "val2": "Option Two",
                    "val3": "Option Three"
                },
                "default": "val3"
            },
            "number": {
                "label": "Number",
                "type": "number",
                "attrs": {
                    "required": true
                }                
            },
            "checkbox": {
                "label": "Checkbox",
                "type": "checkbox",
                "on": "Enabled",
                "off": "Disabled",
                "default": "on"
            },
            "radio": {
                "label": "Radio",
                "type": "radio",
                "options": {
                    "center": "Center",
                    "left": "Left",
                    "right": "Right"
                },
                "default": "left"
            },
            "image": {
                "label": "Image",
                "type": "media",
                "remove_delay": 5000
            },
            "rich_text": {
                "label": "Rich Text Box",
                "type": "richtext",
                "min_height": 150
            },
            "code": {
                "label": "Ace Editor",
                "type": "code",
                "mode": "html",
                "theme": "monokai",
                "minlines": 3,
                "maxlines": 20
            },
            "seo_title": {
                "label": "Title",
                "type": "text",
                "class": "col-md-12",
                "attrs": {
                    "required": true     
                }
            },
            "meta_description": {
                "label": "Description",
                "type": "textarea",
                "class": "col-md-6"
            },
            "meta_keywords": {
                "label": "Keywords",
                "type": "textarea",
                "class": "col-md-6"
            }
        }
    }
}
```

Where:  
*many:* Many rows allowed if true.
*columns:* Number of columns for fields set separation (many sets in one row). The values are: 1 - 6.   
*source:* Model name (class) for the data storage. If not present used local model field.  
Storage model should have next fields:  
*row_id* - keeps local inline fields set row id.
*model* - master model name.  
*model_id* - master model id.  
*model_field* - master model related field.  
*order* - a sorting field, keeps row order.  

Also, all custom inline fields you need with specific types.

Allowed fields:  
*number* - db storage type: number    
*text* - db storage type: varchar  
*textarea* - db storage type: text  
*richtext* - db storage type: text, supported option: min_height   
*code* - db storage type: text, supported options: *mode*, *theme*, *minlines* and *maxlines*    
*media* - db storage type: text (handles for media-library), can hold multiple media files with sorting. Supported options: *remove_delay* - pause in ms before media file will be removed (after clicking remove button).     
*date* - db storage type: date  
*checkbox* - db storage type: tinyint, supported options: see the example above  
*radio* - db storage type: text, supported options: see the example above  
*select* - db storage type: text, supported options: see the example above     

Each field also can have additional common options:  
*class* - a wrapper class, to organize the layout inside the set.
*attrs* - a list of any you need html attributes for the field.

>Retrieving stored data from the field:  

Prepare your model and add *InlineSetTrait* trait:
```php
...
use MonstreX\VoyagerExtension\Traits\InlineSetTrait;
...
class Page extends Model
{
    use InlineSetTrait;
    ...
}

```
Then just use trait method:  

```php
$data = $post->getInlineSet('news_sections');
```

Where *news_sections* is the field name keeps inline fields set.

Also, the trait is necessary to remove related sources data in corresponding table during model delete.    


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

### Dropdown browse filters

Needed for filtering data in a browse mode. Uses on relationship "belongs to" field type only.

```json
{
    "browse_filter": true
}
```
![Browse filter](/docs/images/browse-filter.png)

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

Using **browse_inline_editor** you can enable an inline switcher in a browse view mode.
After that you can change the field value directly (by clicking on it) from a browse mode without entering an edit mode.

![Inline checkbox switcher](/docs/images/inline-checkbox.png)

```json
{
    "browse_inline_editor": true,
    "on": "Active",
    "off": "Disabled",
    "checked": true
}
```

### Inline text, number editors

Using **browse_inline_editor** you can also enable an inline editors in a browse view mode for text and number types.
After that you can change the field value directly from a browse mode without entering to edit mode.

```json
{
    "browse_inline_editor": true
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

Set browse order you need in the Extra Options of BREAD Builder.

```json
{
    "browse_order": [
        "status",
        "id",
        "title",
        "block_belongsto_block_region_relationship",
        "urls",
        "created_at"
    ]
}
```
>Notice: a voyager relation field should use their own naming convention like **block_belongsto_block_region_relationship**.

Also you can change the column order in a browse mode using this option:
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
However, you can use built-in localization helper and retrieve translated substring from a field content:

```php
$field_data = '{{en}}English title {{ru}}Russian title';
// or 
$field_data = '[[en]]English title [[ru]]Russian title';
...
$field_title_en = str_trans($field_data);
$field_title_ru = str_trans($field_data,'ru');
```

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

[link-packagist]: https://packagist.org/packages/monstrex/testpackage
[link-downloads]: https://packagist.org/packages/monstrex/testpackage
