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
styles.forEach(name => mix.sass(name, 'assets/css'))

scripts.forEach(name => mix.js(name, 'assets/js'))

adminScripts.forEach(name => mix.js(name, 'assets/js/admin'))

mix.react('assets/babel/calendar.jsx', 'assets/js')

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
  files: ['assets/js/**/*.js', 'assets/css/*.css']
})

mix.webpackConfig({
  externals,
  optimization: {
    minimize: false
  },
  output: {
    pathinfo: false
  }
})

mix.options({
  processCssUrls: false,
  postCss: [
    require('css-mqpacker')()
  ]
})

mix.setPublicPath('./')
mix.sourceMaps(false, 'source-map')
mix.disableSuccessNotifications()
