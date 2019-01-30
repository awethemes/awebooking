const glob = require('glob')
const mix = require('laravel-mix')

/**
 * The webpack externals library.
 *
 * @type {object}
 */
const externals = {
  'react': 'React',
  'react-dom': 'ReactDOM',
  'ko': 'window.ko',
  'jquery': 'jQuery',
  'moment': 'moment',
  'lodash': 'lodash',
  'popper.js': 'Popper',
  '@wordpress/api-fetch': { this: [ 'wp', 'apiFetch' ] },
  '@wordpress/blocks': { this: [ 'wp', 'blocks' ] },
  '@wordpress/components': { this: [ 'wp', 'components' ] },
  '@wordpress/compose': { this: [ 'wp', 'compose' ] },
  '@wordpress/data': { this: [ 'wp', 'data' ] },
  '@wordpress/element': { this: [ 'wp', 'element' ] },
  '@wordpress/editor': { this: [ 'wp', 'editor' ] },
  '@wordpress/i18n': { this: [ 'wp', 'i18n' ] },
  '@wordpress/url': { this: [ 'wp', 'url' ] },
}

/**
 * File paths.
 */
const styles = glob.sync('assets/scss/*.scss')
const scripts = glob.sync('assets/babel/*.js')
const adminScripts = glob.sync('assets/babel/admin/*.js')

/**
 * Styles and scripts
 */
// styles.forEach(name => mix.sass(name, 'assets/css'))

// scripts.forEach(name => mix.js(name, 'assets/js'))

// adminScripts.forEach(name => mix.js(name, 'assets/js/admin'))

// mix.react('assets/babel/calendar.jsx', 'assets/js')
mix.react('blocks/awebooking-rooms/index.jsx', 'blocks/awebooking-rooms')
mix.react('blocks/awebooking-search-form/index.jsx', 'blocks/awebooking-search-form')

if (mix.inProduction()) {
  mix.version()
}

/**
 * Mix Options
 *
 * @see https://laravel-mix.com/docs/4.0/options
 */
mix.browserSync({
  proxy: process.env.MIX_BROWSER_SYNC_PROXY || 'awebooking.local',
  files: ['assets/js/**/*.js', 'assets/css/*.css', '']
})

Mix.listen('configReady', (webpackConfig) => {
  if (Mix.isUsing('hmr')) {
    // Remove leading '/' from entry keys
    webpackConfig.entry = Object.keys(webpackConfig.entry).reduce((entries, entry) => {
      entries[entry.replace(/^\//, '')] = webpackConfig.entry[entry];
      return entries;
    }, {});

    // Remove leading '/' from ExtractTextPlugin instances
    webpackConfig.plugins.forEach((plugin) => {
      if (plugin.constructor.name === 'ExtractTextPlugin') {
        plugin.filename = plugin.filename.replace(/^\//, '');
      }
    });
  }
});

mix.webpackConfig({
  externals,
  optimization: {
    // minimize: false
  },
  output: {
    // pathinfo: false,
    libraryTarget: 'this',
  }
})

mix.options({
  processCssUrls: false,
  hmrOptions: {
    host: 'localhost',
    port: '8089'
  },
  postCss: [
    require('css-mqpacker')()
  ]
})

mix.setPublicPath('./')
mix.sourceMaps(false, 'source-map')
mix.disableSuccessNotifications()
