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
    | Use original tools/bread/edit-add.blade.php or use extended one
    |
    */

    'legacy_bread_list' => false,

    /*
    |
    | Use sticky action panel (if 'enabled' = true) instead the original action buttons.
    | If 'autohide' = true will show and hide automatically on mouse over/leave events.
    | Uses in edit-add.blade.php and tools/bread/edit-add.blade.php
    |
    */

    'sticky_action_panel' => [
        'enabled' => true,
        'autohide' => false,
    ],

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
    |
    */

    'use_media_path_generator' => true,
    'use_media_url_generator' => true,

    /*
    |
    | Use Str::slug function on media file names before saving
    |
    */

    'slug_filenames' => true,

];
