var gulp         = require('gulp');
var browserSync  = require('browser-sync').create();
var sass         = require('gulp-sass');
var sourcemaps   = require('gulp-sourcemaps');
var autoprefixer = require('gulp-autoprefixer');
var notify       = require('gulp-notify');
var cssnano      = require('gulp-cssnano');
var uglify       = require('gulp-uglify');
var wppot        = require('gulp-wp-pot');
var del          = require('del');

/**
 * Handle errors and alert the user.
 */
function handleErrors() {
  var args = Array.prototype.slice.call(arguments);

  notify.onError({
    title: 'Task Failed! See console.',
    message: "\n\n<%= error.message %>",
  }).apply(this, args);

  // Prevent the 'watch' task from stopping
  this.emit('end');
}

/**
 * Generates the "pot" file.
 */
gulp.task('wp-pot', function () {
  return gulp.src(['inc/**/*.php', 'templates/**/*.php', '*.php'])
    .pipe(wppot({
      'domain': 'awebooking',
      'package': 'awebooking/awebooking',
    }))
    .pipe(gulp.dest('languages/awebooking.pot'));
});

/**
 * Compile SASS and run stylesheet through autoprefixer.
 */
gulp.task('scss', function() {
  return gulp.src('assets/sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', handleErrors))
    .pipe(autoprefixer())
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('assets/css'))
    .pipe(browserSync.stream({ match: '**/*.css' }));
});

/**
 * Start browsersync and watch change files.
 */
gulp.task('watch', function () {
  browserSync.init({
    files: ['{inc}/**/*.php', '*.php'],
    proxy: 'awebooking.dev',
    snippetOptions: {
      whitelist: ['/wp-admin/admin-ajax.php'],
      // blacklist: ['/wp-admin/**']
    }
  });

  gulp.watch('assets/sass/**/*.scss', gulp.parallel('scss'));
});

gulp.task('build', gulp.series('scss', 'wp-pot'));
gulp.task('default', gulp.series('build', 'watch'));
