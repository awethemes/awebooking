'use strict';

const gulp         = require('gulp');
const plumber      = require('gulp-plumber');
const notify       = require('gulp-notify');
const sass         = require('gulp-sass');
const gcmq         = require('gulp-group-css-media-queries');
const sourcemaps   = require('gulp-sourcemaps');
const autoprefixer = require('gulp-autoprefixer');
const bro          = require('gulp-bro');
const babelify     = require('babelify');
const potgen       = require('gulp-wp-pot');
const browserSync  = require('browser-sync').create();
const map          = require('lodash.map');
const pkg          = require('./package.json');

/**
 * Handle errors and alert the user.
 */
function handleErrors() {
  var args = Array.prototype.slice.call(arguments);

  notify.onError({
    title: 'Task Failed! See console.',
    message: "<%= error.message %>",
  }).apply(this, args);

  // Prevent the 'watch' task from stopping
  this.emit('end');
}

gulp.task('scss', () => {
  return gulp.src('assets/scss/*.scss')
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(gcmq())
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('assets/css'))
    .pipe(browserSync.stream({ match: '**/*.css' }));
});

gulp.task('babel', () => {
  return gulp.src(['assets/babel/*.js', 'assets/babel/admin/*.js'], { base: 'assets/babel' })
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(sourcemaps.init())
    .pipe(bro({ error: 'emit', transform: [ babelify.configure({ presets: ['es2015'] }) ] }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('assets/js'))
    .pipe(browserSync.stream({ match: '**/*.js' }));
});

gulp.task('i18n', () => {
  gulp.src(['**/*.php', '!vendor/**', '!tests/**'])
    .pipe(plumber())
    .pipe(potgen({ domain: 'awebooking', package: 'AweBooking' }))
    .pipe(gulp.dest('languages/awebooking.pot'));
});

gulp.task('copy', () => {
  return map(pkg.copyFiles, (files, vendor) => {
    return gulp.src(files).pipe(gulp.dest('assets/vendor/' + vendor));
  });
});

gulp.task('watch', () => {
  browserSync.init({
    proxy: 'awebooking.local',
  });

  gulp.watch('assets/scss/**/*.scss', gulp.parallel('scss'));
  gulp.watch('assets/babel/**/*.js', gulp.parallel('babel'));
});

gulp.task('default', gulp.series(['scss', 'babel', 'i18n', 'copy']));
