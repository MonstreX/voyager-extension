<?php

use TCG\Voyager\Facades\Voyager;

Route::group(['as' => 'voyager.'], function () {

    $extensionController = '\MonstreX\VoyagerExtension\Controllers\VoyagerExtensionController';

    try {
        foreach (Voyager::model('DataType')::all() as $dataType) {
            Route::post($dataType->slug . '/sort/media', $extensionController . '@sort_media')->name($dataType->slug . '.ext-media.sort');
            Route::post($dataType->slug . '/update/media', $extensionController . '@update_media')->name($dataType->slug . '.ext-media.update');
            Route::post($dataType->slug . '/change/media', $extensionController . '@change_media')->name($dataType->slug . '.ext-media.change');
            Route::post($dataType->slug . '/remove/media', $extensionController . '@vext_remove_media')->name($dataType->slug . '.ext-media.remove');
            Route::post($dataType->slug . '/form/media', $extensionController . '@load_image_form')->name($dataType->slug . '.ext-media.form');
        }
    } catch (\InvalidArgumentException $e) {
        throw new \InvalidArgumentException("Custom routes hasn't been configured because: " . $e->getMessage(), 1);
    } catch (\Exception $e) {
        // do nothing, might just be because table not yet migrated.
    }

    //Load translations
    Route::get('admin/voyager-extension-translations', $extensionController . '@load_translations')->name('voyager_extension_translations');

    //Asset Routes
    Route::get('admin/voyager-extension-assets', ['uses' => $extensionController . '@assets', 'as' => 'voyager_extension_assets']);

});


