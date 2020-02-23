let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.scripts([
    'resources/assets/js/codemirror/codemirror.js',
    'resources/assets/js/codemirror/mode/xml/xml.js',
    'resources/assets/js/codemirror/mode/javascript/javascript.js',
    'resources/assets/js/codemirror/mode/css/css.js',
    'resources/assets/js/mode/htmlmixed/htmlmixed.js',
    'resources/assets/js/zebra-dialog/zebra_dialog.min.js',
    'resources/assets/js/sortable.min.js',
],  'publishable/assets/js/vendor.js');

mix.babel([
    'resources/assets/js/scripts.js',
], 'publishable/assets/js/app.js');

mix.less('resources/assets/less/app.less', 'publishable/assets/css/app.css').options({
    processCssUrls: false
});

if (mix.inProduction()) {
    mix.version();
}
