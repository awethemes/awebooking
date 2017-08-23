let mix = require('laravel-mix').mix;

// Complide scss and js
mix.js('assets/jssrc/admin/awebooking.js', 'assets/js/admin')
   .js('assets/jssrc/admin/booking.js', 'assets/js/admin')

mix.sass('assets/sass/admin.scss', 'assets/css');

if (mix.inProduction()) {
  mix.version();
}

// Setup project.
mix.sourceMaps();
mix.setPublicPath('assets');
mix.disableSuccessNotifications();

mix.options({
  processCssUrls: false
});

mix.browserSync({
  proxy: 'awebooking.dev',
  files: [
    'inc/**/*.php',
    'templates/**/*.php',
    'assets/css/*.css',
    'assets/js/**/*.js',
  ]
});
