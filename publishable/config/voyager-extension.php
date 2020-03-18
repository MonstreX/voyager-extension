<?php

return [

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
    | Here you can enable or disable the custom path generator for medialibrary images
    | at MonstreX\VoyagerExtension\Generators\MediaLibraryPathGenerator
    |
    */

    'use_media_path_generator' => true,
];
