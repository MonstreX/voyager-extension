<?php

namespace MonstreX\VoyagerExtension;

use Illuminate\Http\Request;
use Illuminate\Events\Dispatcher;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\AliasLoader;
use Config;
use Lang;


use MonstreX\VoyagerExtension\Generators\MediaLibraryPathGenerator;
use TCG\Voyager\Facades\Voyager;

use MonstreX\VoyagerExtension\FormFields\AdvImageFormField;
use MonstreX\VoyagerExtension\FormFields\AdvMediaFilesFormField;
use MonstreX\VoyagerExtension\FormFields\AdvSelectDropdownTreeFormField;
use MonstreX\VoyagerExtension\Actions\CloneAction;
use MonstreX\VoyagerExtension\Contracts;

use MonstreX\VoyagerExtension\Facades;


class VoyagerExtensionServiceProvider extends ServiceProvider
{

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

        // Create Common Routes
        $this->loadRoutesFrom(__DIR__ . '/../routes/routes.php');

        // Create Voyager Routes
        app(Dispatcher::class)->listen('voyager.admin.routing', function ($router) {
            $this->addRoutes($router);
        });

        $this->loadConfig();

        $this->loadTranslationsFrom(__DIR__.'/../publishable/lang', 'voyager-extension');

        $this->loadTranslationsJS();

        $this->loadViews();

        $this->registerActions();

        $this->registerFields();

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('Vext', Facades\VoyagerExtension::class);

        $this->app->singleton('vext', function () {
            return new VoyagerExtension();
        });


        $this->loadHelpers();

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
            //$this->registerConsoleCommands();
        }

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerController',
            'MonstreX\VoyagerExtension\Controllers\VoyagerExtensionRootController'
        );

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerBaseController',
            'MonstreX\VoyagerExtension\Controllers\VoyagerExtensionBaseController'
        );

        $path = resource_path(__DIR__.'/../publishable/lang/en');


    }


    /**
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        // Publish Assets
        //$this->publishes([dirname(__DIR__).'/publishable/assets' => public_path('vendor/voyager-extension/assets')], 'public');

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
            //voyager_extension_asset('js/tinytoggle/css/tinytoggle.min.css'),
            voyager_extension_asset('css/app.css'),
        ]);

        Config::set('voyager.additional_js', [
            //voyager_extension_asset('js/tinytoggle/jquery.tinytoggle.min.js'),
            voyager_extension_asset('js/app.js'),
        ]);
    }


    /**
     * Register Views
     */
    private function loadViews()
    {
        // Bind Views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'voyager-extension');

        // Listen to when the BREAD edit-add is loading and set the view listener
        // to inject a script to handle Voyager Extension functional
        Voyager::onLoadingView('voyager::bread.edit-add', function () {
            app(Dispatcher::class)->listen('composing: voyager::master', function () {
                view('voyager-extension::master.templates')->render();
                view('voyager-extension::master.js')->render();
            });
        });

        // Override Legacy Views
        View::composer('voyager::bread.browse', function ($view) {
            view('voyager-extension::bread.browse')->with($view->gatherData())->render();
        });

        View::composer('voyager::bread.read', function ($view) {
            view('voyager-extension::bread.read')->with($view->gatherData())->render();
        });

    }

    /**
     * Register new actions.
     */
    private function registerActions()
    {
        if(config('voyager-extension.clone_record.enabled')) {
            Voyager::addAction(CloneAction::class);
        }
    }

    /**
     * Register new fields.
     */
    private function registerFields()
    {

        Voyager::addFormField(AdvImageFormField::class);
        Voyager::addFormField(AdvMediaFilesFormField::class);
        Voyager::addFormField(AdvSelectDropdownTreeFormField::class);
    }

    /**
     * Load helpers.
     */
    protected function loadHelpers()
    {
        foreach (glob(__DIR__.'/Helpers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Prepare translations for frontend JS
     */
    protected function loadTranslationsJS()
    {
        Cache::rememberForever('translations', function () {
            return [
                'bread' => Lang::get('voyager-extension::bread'),
            ];
        });
    }

    /*
     *  Add Routes
     */
    public function addRoutes($router){

        $extensionController = '\MonstreX\VoyagerExtension\Controllers\VoyagerSiteController';
        $extensionVoyagerController = '\MonstreX\VoyagerExtension\Controllers\VoyagerExtensionBaseController';

        try {
            foreach (Voyager::model('DataType')::all() as $dataType) {
                $router->post($dataType->slug . '/sort/media', $extensionController . '@sort_media')->name($dataType->slug . '.ext-media.sort');
                $router->post($dataType->slug . '/update/media', $extensionController . '@update_media')->name($dataType->slug . '.ext-media.update');
                $router->post($dataType->slug . '/change/media', $extensionController . '@change_media')->name($dataType->slug . '.ext-media.change');
                $router->post($dataType->slug . '/remove/media', $extensionController . '@remove_media')->name($dataType->slug . '.ext-media.remove');
                $router->post($dataType->slug . '/form/media', $extensionController . '@load_image_form')->name($dataType->slug . '.ext-media.form');

                $router->post($dataType->slug . '/{id}/clone', $extensionVoyagerController . '@clone')->name($dataType->slug . '.clone');
                $router->post($dataType->slug . '/{id}/record/update', $extensionVoyagerController . '@recordUpdate')->name($dataType->slug . '.ext-record-update');
                $router->post($dataType->slug . '/records/order', $extensionVoyagerController . '@recordsOrder')->name($dataType->slug . '.ext-records-order');
            }
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Custom routes hasn't been configured because: " . $e->getMessage(), 1);
        } catch (\Exception $e) {
            // do nothing, might just be because table not yet migrated.
        }

    }


}
