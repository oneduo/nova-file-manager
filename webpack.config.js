const path = require('path')

module.exports = () => {
  const prefix = process.env.MIX_NOVA_PREFIX || '../..'

  return {
    externals: {
      vue: 'Vue',
    },

    resolve: {
      alias: {
        'laravel-nova': path.join(__dirname, prefix, '/laravel/nova/resources/js/mixins/packages.js'),
        '@': path.join(__dirname, '/resources/js'),
      },
    },

    output: {
      uniqueName: 'nova-file-manager',
    },
  }
}
