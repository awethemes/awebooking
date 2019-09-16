const { dest, src, parallel } = require('gulp');
const uglify = require('gulp-uglify');
const cleanCSS = require('gulp-clean-css');
const rename = require('gulp-rename');
const pkg = require('./package.json');

function minifyJs() {
  return src(['assets/js/**/*.js', '!assets/js/**/*.min.js'])
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(dest('assets/js'));
}

function minifyCss() {
  return src(['assets/css/*.css', '!assets/css/*.min.css'])
    .pipe(cleanCSS())
    .pipe(rename({ suffix: '.min' }))
    .pipe(dest('assets/css'));
}

function copyVendor(cb) {
  const copyFiles = pkg.copyFiles || {};

  Object.keys(copyFiles).forEach(vendor => {
    return src(copyFiles[vendor], { cwd: 'node_modules/' })
      .pipe(dest(`assets/vendor/${vendor}`));
  });

  cb();
}

exports.copy = copyVendor;
exports.minify = parallel(minifyJs, minifyCss);
exports.default = parallel(minifyJs, minifyCss);
