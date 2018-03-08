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
   .js('assets/jssrc/admin/edit-room-type.js', 'assets/js/admin')
   .js('assets/jssrc/admin/manager-pricing.js', 'assets/js/admin')
   .js('assets/jssrc/admin/manager-availability.js', 'assets/js/admin')
   .js('assets/jssrc/admin/schedule-calendar.js', 'assets/js/admin');

mix.copy('node_modules/sweetalert2/dist/sweetalert2.css', 'assets/css/sweetalert2.css', false);
mix.copy('node_modules/sweetalert2/dist/sweetalert2.min.js', 'assets/js/sweetalert2/sweetalert2.min.js', false);
mix.copy('node_modules/magnific-popup/dist/magnific-popup.css', 'assets/css/magnific-popup.css', false);
mix.copy('node_modules/magnific-popup/dist/jquery.magnific-popup.min.js', 'assets/js/magnific-popup/jquery.magnific-popup.min.js', false);
mix.copy('node_modules/flatpickr/dist/themes/confetti.css', 'assets/css/flatpickr.css', false);
mix.copy('node_modules/waypoints/lib/noframework.waypoints.min.js', 'assets/js/waypoints/waypoints.min.js', false);

mix.extract(['vue', 'form-serialize', 'popper.js', 'tooltip.js', 'flatpickr']);

if (mix.inProduction()) {
  mix.version();
}

mix.browserSync({
  proxy: 'awebooking.local',
  files: [
    'inc/**/*.php',
    'templates/**/*.php',
    'assets/css/**/*.css',
    // 'assets/js/**/*.js',
  ]
});
