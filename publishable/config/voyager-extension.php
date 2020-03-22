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
    |
    */
    'clone_record' => [
        'enabled' => true,
        'reset_types' => ['image', 'multiple_images','file'],
        'suffix_fields' => ['title','name','slug'],
    ],

    /*
    |
    | You can enable or disable the custom path generator for medialibrary images
    | at MonstreX\VoyagerExtension\Generators\MediaLibraryPathGenerator
    |
    */

    'use_media_path_generator' => true,
];
