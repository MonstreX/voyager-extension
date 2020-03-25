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

mix.js('resources/assets/js/app.js', 'publishable/assets/js');

mix.copy('node_modules/zebra_dialog/dist/css/flat', 'publishable/assets/js/zebra_dialog/themes/flat')
.copy('node_modules/codemirror/lib', 'publishable/assets/js/codemirror/lib')
.copy('node_modules/codemirror/addon', 'publishable/assets/js/codemirror/addon')
.copy('node_modules/codemirror/mode', 'publishable/assets/js/codemirror/mode')
.copy('node_modules/codemirror/theme', 'publishable/assets/js/codemirror/theme');

mix.less('resources/assets/less/app.less', 'publishable/assets/css/app.css').options({ processCssUrls: false });
