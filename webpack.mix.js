let mix = require('laravel-mix')

mix
    .setPublicPath('dist')
    .js('resources/js/tool.js', 'js').vue()
    .sass('resources/sass/tool.sass', 'css')
    .webpackConfig(require('./webpack.config'));
