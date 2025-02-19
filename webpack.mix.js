const mix = require('laravel-mix');

mix.js('resources/js/nav.js', 'public/js')
   .css('resources/css/app.css', 'public/css');