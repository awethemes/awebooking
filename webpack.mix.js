let mix = require('laravel-mix').mix;

// Complide scss and js
mix.js('assets/jssrc/admin/awebooking.js', 'assets/js/admin')
   .js('assets/jssrc/admin/edit-booking.js', 'assets/js/admin')
   .js('assets/jssrc/admin/edit-service.js', 'assets/js/admin')
   .js('assets/jssrc/admin/edit-room-type.js', 'assets/js/admin')
   .js('assets/jssrc/admin/manager-pricing.js', 'assets/js/admin')
   .js('assets/jssrc/admin/manager-availability.js', 'assets/js/admin')
   .extract(['vue', 'form-serialize']);

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