<?php

use Illuminate\Support\Str;
use TCG\Voyager\Facades\Voyager;

Route::group(['as' => 'voyager.'], function () {
    $namespacePrefix = '\\' . config('voyager.controllers.namespace') . '\\';
    try {
        foreach (Voyager::model('DataType')::all() as $dataType) {
            $breadController = $dataType->controller
                ? Str::start($dataType->controller, '\\')
                : $namespacePrefix . 'VoyagerBaseController';

            Route::post($dataType->slug . '/sort', $breadController . '@sort_media')->name($dataType->slug . '.media.sort');
            Route::post($dataType->slug . '/update', $breadController . '@update_media')->name($dataType->slug . '.media.update');
            Route::post($dataType->slug . '/load/content', $breadController . '@load_content')->name($dataType->slug . '.content.load');
        }
    } catch (\InvalidArgumentException $e) {
        throw new \InvalidArgumentException("Custom routes hasn't been configured because: " . $e->getMessage(), 1);
    } catch (\Exception $e) {
        // do nothing, might just be because table not yet migrated.
    }
});


