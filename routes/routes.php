<?php

use TCG\Voyager\Facades\Voyager;

Route::group(['as' => 'voyager.'], function () {

    $extensionController = '\MonstreX\VoyagerExtension\Controllers\VoyagerExtensionController';

    try {
        foreach (Voyager::model('DataType')::all() as $dataType) {
            Route::post($dataType->slug . '/sort', $extensionController . '@sort_media')->name($dataType->slug . '.media.sort');
            Route::post($dataType->slug . '/update', $extensionController . '@update_media')->name($dataType->slug . '.media.update');
            Route::post($dataType->slug . '/load/content', $extensionController . '@load_image_form')->name($dataType->slug . '.media.form.load');
        }
    } catch (\InvalidArgumentException $e) {
        throw new \InvalidArgumentException("Custom routes hasn't been configured because: " . $e->getMessage(), 1);
    } catch (\Exception $e) {
        // do nothing, might just be because table not yet migrated.
    }

    //Asset Routes
    Route::get('admin/voyager-extension-assets', ['uses' => $extensionController . '@assets', 'as' => 'voyager_extension_assets']);

});


