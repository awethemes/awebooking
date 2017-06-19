'use strict';

var del          = require('del');
var gulp         = require('gulp');
var bs           = require('browser-sync').create();
var sass         = require('gulp-sass');
var cssnano      = require('gulp-cssnano');
var autoprefixer = require('gulp-autoprefixer');

var uglify       = require('gulp-uglify');
var concat       = require('gulp-concat');
var include      = require('gulp-include');

var sourcemaps   = require('gulp-sourcemaps');
var gutil        = require('gulp-util');
var changed      = require('gulp-changed');
var notify       = require('gulp-notify');
var plumber      = require('gulp-plumber');
var sort         = require('gulp-sort');
var wppot        = require('gulp-wp-pot');

/**
 * Handle errors and alert the user.
 */
function handleErrors() {
  var args = Array.prototype.slice.call(arguments);

  notify.onError({
    'title': 'Task Failed! See console.',
    'message': "\n\n<%= error.message %>",
    'sound': 'Sosumi' // See: https://github.com/mikaelbr/node-notifier#all-notification-options-with-their-defaults
  }).apply(this, args);

  gutil.beep(); // Beep 'sosumi' again

  // Prevent the 'watch' task from stopping
  this.emit('end');
}

gulp.task('wp-pot', function () {
  return gulp.src(['inc/**/*.php', 'i18n/*.php'])
    .pipe(plumber({ 'errorHandler': handleErrors }))
    .pipe(sort())
    .pipe(wppot({
      'domain': 'awebooking',
      'package': 'awethemes/awebooking',
    }))
    .pipe(gulp.dest('i18n/languages/awebooking.pot'));
});

gulp.task('sass', function () {
  return gulp.src('assets/sass/*.scss')
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(sourcemaps.init())
    .pipe(sass({ errLogToConsole: true }))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('assets/css'))
    .pipe(bs.stream({ match: '**/*.css' }));
});

gulp.task('watch', function () {
  // bs.init({
  //   // files: ['inc/**/*.php', '*.php'],
  //   proxy: 'wp.dev',
  //   snippetOptions: {
  //     // whitelist: ['/wp-admin/admin-ajax.php'],
  //     // blacklist: ['/wp-admin/**'],
  //   }
  // });

  // gulp.watch(['assets/js/**/*.js'], gulp.series('js'));
  gulp.watch(['assets/sass/**/*.scss'], gulp.series('sass'));
});

gulp.task('build', gulp.parallel('sass'));
gulp.task('default', gulp.series('build', 'watch'));
