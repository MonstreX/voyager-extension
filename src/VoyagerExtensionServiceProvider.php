<?php

namespace MonstreX\VoyagerExtension;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;
use Config;

use Illuminate\Support\Str;
use MonstreX\VoyagerExtension\Generators\MediaLibraryPathGenerator;
use TCG\Voyager\Facades\Voyager;

use MonstreX\VoyagerExtension\FormFields\KeyValueJsonFormField;
use MonstreX\VoyagerExtension\FormFields\AdvImageFormField;
use MonstreX\VoyagerExtension\FormFields\AdvImagesGalleryFormField;

class VoyagerExtensionServiceProvider extends ServiceProvider
{

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        // Load Configs
        $this->loadConfig();

        // Load Translations
        $this->loadTranslationsFrom(__DIR__.'/../publishable/lang', 'voyager-extension');

        // Bind Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager-extension');

        // Insert blade injections into the master voyager blade template
        app(Dispatcher::class)->listen('composing: voyager::master', function () {
            view('voyager-extension::master.js')->render();
        });

        // Add new fields
        $this->registerFields();

        // Load Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            //$this->registerConsoleCommands();
        }

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerBaseController',
            'MonstreX\VoyagerExtension\Controllers\VoyagerExtensionBaseController'
        );

    }

    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        // Publish Assets
        $this->publishes([dirname(__DIR__).'/publishable/assets' => public_path('vendor/voyager-extension/assets')],
            'public');
        // Publish Config
        $this->publishes([dirname(__DIR__).'/publishable/config/voyager-extension.php' => config_path('voyager-extension.php')]);
    }

    /**
     * Register configs.
     */
    private function loadConfig()
    {
        // Add custom Path generator for medialibrary files if enabled
        if (config('voyager-extension.use_media_path_generator')) {
            Config::set('medialibrary.path_generator', MediaLibraryPathGenerator::class);
        }

        // Add CSS and JS to the Voyager's config
        Config::set('voyager.additional_css', [
            'vendor/voyager-extension/assets/js/zebra-dialog/css/flat/zebra_dialog.min.css',
            'vendor/voyager-extension/assets/css/styles.css',
        ]);

        Config::set('voyager.additional_js', [
            'vendor/voyager-extension/assets/js/zebra-dialog/zebra_dialog.min.js',
            'vendor/voyager-extension/assets/js/sortable.min.js',
            'vendor/voyager-extension/assets/js/scripts.js',
        ]);
    }

    /**
     * Register new fields.
     */
    private function registerFields()
    {
        Voyager::addFormField(AdvImageFormField::class);
        Voyager::addFormField(AdvImagesGalleryFormField::class);
        Voyager::addFormField(KeyValueJsonFormField::class);
    }

}
