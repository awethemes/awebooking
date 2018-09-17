'use strict';

const gulp         = require('gulp')
const debug        = require('gulp-debug')
const plumber      = require('gulp-plumber')
const notify       = require('gulp-notify')
const sourcemaps   = require('gulp-sourcemaps')
const sass         = require('gulp-sass')
const postcss      = require('gulp-postcss')
const autoprefixer = require('gulp-autoprefixer');
const gcmq         = require('gulp-group-css-media-queries')
const cleanCSS     = require('gulp-clean-css')
const rollup       = require('gulp-better-rollup')
const uglify       = require('gulp-uglify')
const rename       = require('gulp-rename')
const potgen       = require('gulp-wp-pot')
const browserSync  = require('browser-sync').create()
const del          = require('del')
const map          = require('lodash.map')
const pkg          = require('./package.json')

const rollupConfig = () => {
  const resolve  = require('rollup-plugin-node-resolve')
  const commonjs = require('rollup-plugin-commonjs')
  const babel    = require('rollup-plugin-babel')

  return {
    rollup: require('rollup'),
    external: Object.keys(pkg.globals),
    plugins: [
      resolve(),
      commonjs(),
      babel({
        babelrc: false,
        runtimeHelpers: true,
        externalHelpers: true,
        presets: ['@babel/preset-env']
      }),
    ]
  }
}

/**
 * Handle errors and alert the user.
 */
const handleErrors = (r) => {
  notify.onError('ERROR: <%= error.message %>\n')(r)
}

gulp.task('scss', () => {
  return gulp.src('assets/scss/*.scss')
     .pipe(debug())
     .pipe(plumber(handleErrors))
     .pipe(sourcemaps.init())
     .pipe(sass().on('error', sass.logError))
     .pipe(autoprefixer())
     .pipe(gcmq())
     .pipe(sourcemaps.write('./'))
     .pipe(gulp.dest('assets/css'))
     .pipe(browserSync.stream({ match: '**/*.css' }))
})

gulp.task('babel', () => {
  return gulp.src(['assets/babel/*.js', 'assets/babel/admin/*.js'], { base: 'assets/babel' })
    .pipe(debug())
    .pipe(plumber(handleErrors))
    .pipe(sourcemaps.init())
    .pipe(rollup(rollupConfig(), {
      format: 'iife',
      globals: pkg.globals || {}
    }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest('assets/js'))
});

gulp.task('minify:js', () => {
  return gulp.src(['assets/js/**/*.js', '!assets/js/**/*.min.js'])
     .pipe(debug())
     .pipe(plumber())
     .pipe(uglify())
     .pipe(rename({ suffix: '.min' }))
     .pipe(gulp.dest('assets/js'))
})

gulp.task('minify:css', () => {
  return gulp.src(['assets/css/*.css', '!assets/css/*.min.css'])
    .pipe(debug())
    .pipe(plumber(handleErrors))
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('assets/css'))
})

gulp.task('i18n', () => {
  return gulp.src(['*.php', 'inc/**/*.php', 'component/**/*.php', 'templates/**/*.php', '!vendor/**', '!tests/**'])
    .pipe(plumber(handleErrors))
    .pipe(potgen({ domain: pkg.name, package: 'AweBooking' }))
    .pipe(gulp.dest(`languages/${pkg.name}.pot`))
})

gulp.task('copy', (done) => {
  map(pkg.copyFiles || {}, (files, vendor) => {
    return gulp.src(files).pipe(gulp.dest('assets/vendor/' + vendor))
  })

  done()
})

gulp.task('clean', () => {
  return del([
    'assets/vendor',
    'assets/js/**/*.{js,map}',
    'assets/css/**/*.{css,map}',
  ])
})

gulp.task('watch', () => {
  browserSync.init({
    open: false,
    proxy: 'awebooking.local',
  })

  gulp.watch('assets/scss/**/*.scss', gulp.series(['scss']))
  gulp.watch('assets/babel/**/*.js', gulp.series(['babel']))
})

gulp.task('js', gulp.series(['babel', 'minify:js']))
gulp.task('css', gulp.series(['scss', 'minify:css']))
gulp.task('default', gulp.series(['clean', 'css', 'js', 'i18n', 'copy']))
