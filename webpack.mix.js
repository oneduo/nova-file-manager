const mix = require('laravel-mix')
const postcssImport = require('postcss-import')
const tailwindcss = require('tailwindcss')

require('./nova.mix')
const config = require('./webpack.config')

mix
  .disableNotifications()
  .setPublicPath('dist')
  .js('resources/js/tool.js', 'js')
  .vue({ version: 3 })
  .postCss('resources/css/tool.css', 'css', [postcssImport(), tailwindcss('tailwind.config.js')])
  .options({
    processCssUrls: false,
  })
  .webpackConfig(config)
  .nova('oneduo/nova-file-manager')
