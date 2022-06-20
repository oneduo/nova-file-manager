const path = require('path')

module.exports = {
  externals: {
    vue: 'Vue',
  },

  resolve: {
    alias: {
      'laravel-nova': path.join(
        __dirname,
        '../../vendor/laravel/nova/resources/js/mixins/packages.js',
      ),
      '@': path.join(__dirname, '/resources/js'),
    },
  },

  output: {
    uniqueName: 'nova-file-manager',
  },
}
