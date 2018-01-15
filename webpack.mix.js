let mix = require('laravel-mix').mix;

// Setup project.
mix.setPublicPath('assets');
mix.disableSuccessNotifications();

mix.options({
  processCssUrls: false
});

// Complide scss and js
mix.sass('assets/sass/admin.scss', 'assets/css')
    .sass('assets/sass/theme.scss', 'assets/css')
    .sass('assets/sass/awebooking.scss', 'assets/css');

mix.js('assets/jssrc/admin/awebooking.js', 'assets/js/admin')
   .js('assets/jssrc/admin/edit-booking.js', 'assets/js/admin')
   .js('assets/jssrc/admin/edit-service.js', 'assets/js/admin')
   .js('assets/jssrc/admin/edit-room-type.js', 'assets/js/admin')
   .js('assets/jssrc/admin/manager-pricing.js', 'assets/js/admin')
   .js('assets/jssrc/admin/manager-availability.js', 'assets/js/admin');

mix.copy('node_modules/magnific-popup/dist/magnific-popup.css', 'assets/css/magnific-popup.css', false);
mix.copy('node_modules/magnific-popup/dist/jquery.magnific-popup.min.js', 'assets/js/magnific-popup/jquery.magnific-popup.min.js', false);

mix.extract(['vue', 'form-serialize', 'popper.js', 'tooltip.js']);

if (mix.inProduction()) {
  mix.version();
} else {
  mix.sourceMaps()
}

mix.browserSync({
  proxy: 'awebooking.local',
  files: [
    'inc/**/*.php',
    'templates/**/*.php',
    'assets/css/**/*.css',
  ]
});
