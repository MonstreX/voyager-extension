<?php

Route::group(['prefix' => config('voyager-extension.assets_path_prefix')], function () {
    Route::group(['as' => 'voyager.'], function () {

        $extensionController = '\MonstreX\VoyagerExtension\Controllers\VoyagerExtensionController';
        //Load translations
        Route::get('voyager-extension-translations', $extensionController . '@load_translations')->name('voyager_extension_translations');

        //Asset Routes
        Route::get('voyager-extension-assets', ['uses' => $extensionController . '@assets', 'as' => 'voyager_extension_assets']);

        //Assets Others
        Route::get('voyager-extension/{alias}', ['uses' => $extensionController . '@assets_regular', 'as' => 'voyager_extension_assets_regular'])->where('alias', '.*');

    });
});
