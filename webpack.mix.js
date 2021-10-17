let mix = require('laravel-mix')

mix
    .js('resources/js/tool.js', 'dist/js').vue({ version: 2 })
    .sass('resources/sass/tool.sass', 'dist/css')
    .webpackConfig(require('./webpack.config'));
