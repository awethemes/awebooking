let mix = require('laravel-mix').mix;

// Complide scss and js
mix.sass('assets/sass/admin.scss', 'assets/css');

mix.js('assets/jssrc/admin/awebooking.js', 'assets/js/admin')
   .js('assets/jssrc/admin/booking.js', 'assets/js/admin');

// Setup project.
mix.sourceMaps();
mix.setPublicPath('assets');
mix.disableSuccessNotifications();

if (mix.inProduction()) {
  mix.version();
}

mix.options({
  processCssUrls: false
});

mix.browserSync({
  proxy: 'awebooking.dev',
  files: [
    'inc/**/*.php',
    'templates/**/*.php',
    'assets/js/**/*.js',
    'assets/css/**/*.css'
  ]
});
