<?php

namespace MonstreX\VoyagerExtension;

use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\AliasLoader;
use Config;
use Lang;

use MonstreX\VoyagerExtension\Generators\MediaLibraryPathGenerator;
use MonstreX\VoyagerExtension\Generators\MediaLibraryUrlGenerator;
use TCG\Voyager\Facades\Voyager;

use MonstreX\VoyagerExtension\FormFields\AdvImageFormField;
use MonstreX\VoyagerExtension\FormFields\AdvMediaFilesFormField;
use MonstreX\VoyagerExtension\FormFields\AdvSelectDropdownTreeFormField;
use MonstreX\VoyagerExtension\FormFields\AdvFieldsGroupFormField;
use MonstreX\VoyagerExtension\FormFields\AdvInlineSetFormField;
use MonstreX\VoyagerExtension\FormFields\AdvJsonFormField;
use MonstreX\VoyagerExtension\FormFields\AdvRelatedFormField;
use MonstreX\VoyagerExtension\FormFields\AdvPageLayoutFormField;

use MonstreX\VoyagerExtension\Actions\CloneAction;

use MonstreX\VoyagerExtension\Facades;


class VoyagerExtensionServiceProvider extends ServiceProvider
{

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

        if (!config('voyager-extension.legacy_bread_list')) {
            $this->app->bind(
                'TCG\Voyager\Models\DataType',
                'MonstreX\VoyagerExtension\Models\DataType'
            );
        }

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerController',
            'MonstreX\VoyagerExtension\Controllers\VoyagerExtensionRootController'
        );

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerBaseController',
            'MonstreX\VoyagerExtension\Controllers\VoyagerExtensionBaseController'
        );

        $this->app->bind(
            'TCG\Voyager\Http\Controllers\VoyagerBreadController',
            'MonstreX\VoyagerExtension\Controllers\VoyagerExtensionBreadController'
        );

        // ToDo: remove if not necessary
        // $path = resource_path(__DIR__.'/../publishable/lang/en');
    }

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->registerPublishableResources();
        }

        // Load migrations
        $this->loadMigrationsFrom(realpath(__DIR__.'/../migrations'));

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
     * Register the publishable files.
     */
    private function registerPublishableResources()
    {
        // Publish Assets
        $this->publishes([dirname(__DIR__).'/publishable/assets' => public_path('vendor/voyager-extension/assets')], 'public');

        // Publish Config
        $this->publishes([dirname(__DIR__).'/publishable/config/voyager-extension.php' => config_path('voyager-extension.php')], 'config');
    }


    /**
     * Register configs.
     */
    private function loadConfig()
    {

        $this->mergeConfigFrom(
            dirname(__DIR__).'/publishable/config/voyager-extension.php', 'voyager-extension'
        );

        // Add custom Path generator for medialibrary files if enabled
        if (config('voyager-extension.use_media_path_generator')) {
            Config::set('media-library.path_generator', MediaLibraryPathGenerator::class);
        }

        // Add custom Url generator for medialibrary files if enabled
        if (config('voyager-extension.use_media_url_generator')) {
            Config::set('media-library.url_generator', MediaLibraryUrlGenerator::class);
        }

        // Add CSS and JS to the Voyager's config
        Config::set('voyager.additional_css', [
            voyager_extension_asset('css/app.css'),
        ]);

        Config::set('voyager.additional_js', [
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

        View::composer('voyager::menus.builder', function ($view) {
            view('voyager-extension::menus.builder')->with($view->gatherData())->render();
        });

        if (!config('voyager-extension.legacy_bread_list')) {
            View::composer('voyager::tools.bread.edit-add', function ($view) {
                view('voyager-extension::tools.bread.edit-add')->with($view->gatherData())->render();
            });
        }

        if (!config('voyager-extension.legacy_edit_add_bread')) {
            View::composer('voyager::bread.edit-add', function ($view) {
                view('voyager-extension::bread.edit-add')->with($view->gatherData())->render();
            });
        }
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
        Voyager::addFormField(AdvFieldsGroupFormField::class);
        Voyager::addFormField(AdvJsonFormField::class);
        Voyager::addFormField(AdvRelatedFormField::class);
        Voyager::addFormField(AdvInlineSetFormField::class);

        // This field depends on voyager-site package
        if (find_package('monstrex/voyager-site')) {
            Voyager::addFormField(AdvPageLayoutFormField::class);
        }

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

        $extensionController = '\MonstreX\VoyagerExtension\Controllers\VoyagerExtensionController';
        $extensionVoyagerController = '\MonstreX\VoyagerExtension\Controllers\VoyagerExtensionBaseController';

        try {

            $router->post( 'menu-items/{id}/record/update', $extensionVoyagerController . '@recordUpdate')->name('menu-items.ext-record-update');
            $router->get( '/records', $extensionVoyagerController . '@getRecords')->name('ext-records-get');

            foreach (Voyager::model('DataType')::all() as $dataType) {
                $router->post($dataType->slug . '/sort/media', $extensionController . '@sort_media')->name($dataType->slug . '.ext-media.sort');
                $router->post($dataType->slug . '/update/media', $extensionController . '@update_media')->name($dataType->slug . '.ext-media.update');
                $router->post($dataType->slug . '/change/media', $extensionController . '@change_media')->name($dataType->slug . '.ext-media.change');
                $router->post($dataType->slug . '/remove/media', $extensionController . '@remove_media')->name($dataType->slug . '.ext-media.remove');
                $router->post($dataType->slug . '/form/media', $extensionController . '@load_image_form')->name($dataType->slug . '.ext-media.form');

                $router->post($dataType->slug . '/{id}/clone', $extensionVoyagerController . '@clone')->name($dataType->slug . '.clone');
                $router->post($dataType->slug . '/{id}/record/update', $extensionVoyagerController . '@recordUpdate')->name($dataType->slug . '.ext-record-update');
                $router->post($dataType->slug . '/records/order', $extensionVoyagerController . '@recordsOrder')->name($dataType->slug . '.ext-records-order');

                $router->get($dataType->slug . '/{id}/record/group', $extensionController . '@load_group_form')->name($dataType->slug . '.ext-group.form');
            }
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException("Custom routes hasn't been configured because: " . $e->getMessage(), 1);
        } catch (\Exception $e) {
            // do nothing, might just be because table not yet migrated.
        }
    }

}
