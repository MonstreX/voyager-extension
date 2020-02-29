<?php

return [
    'adv_image' => [
        'file'             => 'File name:',
        'title'            => 'Title',
        'alt'              => 'Alt',
        'delete'           => 'Delete',
        'not_set_title'    => '...',
        'title_placeholder' => 'Enter a title of the image',
        'alt_placeholder'   => 'Enter an Alt value of the image',
        'help'              => '<strong>Examples:</strong><br/>
                              <b>$image</b> = Post-><strong>getFirstMedia(<i>\':gallery\'</i>)</strong>;<br/>
                              <b>$imageUrl</b> = $image-><strong>getFullUrl()</strong>;<br/>
                              <b>$imageTitle</b> = $image-><strong>getCustomProperty(<i>\'title\'</i>)</strong>;<br/>
                              <b>$imageAlt</b> = $image-><strong>getCustomProperty(<i>\'alt\'</i>)</strong>;<br/>',
    ],
    'adv_media_files' => [
        'remove_selected'      => 'Delete selected images',
        'unmark_selected'      => 'Unmark selected images',
        'select_all'           => 'Select all images',
        'dialog_remove_images' => ' image(s)',
        'dialog_remove_files' => ' media files(s)',
    ],
    'images_removed'          => 'Removed...',
    'images_sorted'           => 'Sorted...',
    'images_updated'          => 'Updated...',

    'dialog_remove_title'     => 'Are you sure?',
    'dialog_remove_message'   => 'You are going to remove: ',
    'dialog_button_remove'    => 'Yes, Delete it!',
    'dialog_button_cancel'    => 'Cancel',

    'error_removing_media'    => 'Error removing media file...',
];
