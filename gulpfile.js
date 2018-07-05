'use strict';

const gulp         = require('gulp');
const plumber      = require('gulp-plumber');
const notify       = require('gulp-notify');
const sourcemaps   = require('gulp-sourcemaps');
const sass         = require('gulp-sass');
const autoprefixer = require('gulp-autoprefixer');
const gcmq         = require('gulp-group-css-media-queries');
const cleanCSS     = require('gulp-clean-css');
const bro          = require('gulp-bro');
const babelify     = require('babelify');
const uglify       = require('gulp-uglify');
const rename       = require('gulp-rename');
const potgen       = require('gulp-wp-pot');
const browserSync  = require('browser-sync').create();
const del          = require('del');
const map          = require('lodash.map');
const pkg          = require('./package.json');

/**
 * Handle errors and alert the user.
 */
function handleErrors() {
  var args = Array.prototype.slice.call(arguments);

  notify.onError({
    title: 'Task Failed! See console.',
    message: '<%= error.message %>',
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
    .pipe(bro({
      error: 'emit',
      transform: [[ babelify.configure({ presets: ['es2015'] }), { global: true } ]]
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('assets/js'))
    .pipe(browserSync.stream({ match: '**/*.js' }));
});

gulp.task('minify:js', () => {
  return gulp.src(['assets/js/**/*.js', '!assets/js/**/*.min.js'])
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/js'));
});

gulp.task('minify:css', () => {
  return gulp.src(['assets/css/*.css', '!assets/css/*.min.css'])
    .pipe(plumber({ errorHandler: handleErrors }))
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/css'));
});

gulp.task('i18n', () => {
  return gulp.src(['*.php', 'inc/**/*.php', 'templates/**/*.php', '!vendor/**', '!tests/**'])
    .pipe(plumber())
    .pipe(potgen({ domain: 'awebooking', package: 'AweBooking' }))
    .pipe(gulp.dest('languages/awebooking.pot'));
});

gulp.task('copy', (done) => {
  map(pkg.copyFiles, (files, vendor) => {
    return gulp.src(files).pipe(gulp.dest('assets/vendor/' + vendor));
  });

  done();
});

gulp.task('clean', () => {
  return del([
    'assets/vendor',
    'assets/js/**/*.{js,map}',
    'assets/css/**/*.{css,map}',
  ]);
});

gulp.task('watch', () => {
  browserSync.init({
    proxy: 'awebooking.local',
  });

  gulp.watch('assets/scss/**/*.scss', gulp.parallel('scss'));
  gulp.watch('assets/babel/**/*.js', gulp.parallel('babel'));
});

gulp.task('js', gulp.series(['babel', 'minify:js']));
gulp.task('css', gulp.series(['scss', 'minify:css']));
gulp.task('default', gulp.series(['clean', 'css', 'js', 'i18n', 'copy']));
