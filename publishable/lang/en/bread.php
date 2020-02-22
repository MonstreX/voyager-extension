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

    'images_sorted'          => 'Sorted...',
    'images_updated'          => 'Updated...',
];
