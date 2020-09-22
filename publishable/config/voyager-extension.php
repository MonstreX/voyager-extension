<?php

return [

    /*
    |
    | Use original edit-add.blade.php or use extended one
    |
    */
    'legacy_edit_add_bread' => false,

    /*
    |
    | CLone Record parameters
    | @params: enabled - if action is available
    |          reset_types - A value of these bread type fields will be cleared
    |          suffix_fields - The suffix '(clone)' will be added to these fields content
    */
    'clone_record' => [
        'enabled' => true,
        'reset_types' => ['image', 'multiple_images','file'],
        'suffix_fields' => ['title','name','slug','key'],
    ],

    /*
    |
    | You can enable or disable the custom path and urls generator for medialibrary images
    | at MonstreX\VoyagerExtension\Generators\MediaLibraryPathGenerator
    | and at MonstreX\VoyagerExtension\Generators\MediaLibraryUrlGenerator
    */

    'use_media_path_generator' => true,
    'use_media_url_generator' => true,
];
