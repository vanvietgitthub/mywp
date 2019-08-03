'use strict';

const gulp              = require('gulp');
const sass              = require('gulp-sass');
const autoprefixer      = require('gulp-autoprefixer');
const sourcemaps        = require('gulp-sourcemaps');
const watch             = require('gulp-watch');
const livereload        = require('gulp-livereload');
const rename            = require('gulp-rename');
const sassGlob          = require('gulp-sass-glob');
const plumber           = require('gulp-plumber');
const csso              = require('gulp-csso');
const stripCssComments  = require('gulp-strip-css-comments');
const concat            = require('gulp-concat');

const styleSRC          = 'scss/main.scss';
const styleURL          = 'wp-content/themes/wda/assets/css';
const styleWatch        = 'scss/**/*.scss';
const styleConcat       = 'wp-content/themes/wda/assets/*.css';

gulp.task('scss', function () {
    return gulp.src( styleSRC )
        .pipe(sassGlob())
        .pipe(plumber())
        .pipe(sourcemaps.init({ loadMap: true }))
        .pipe(sass({
            outputStyle: 'compressed'
        }).on('error', sass.logError))
        .pipe(autoprefixer({ overrideBrowserslist: ["last 10 version"] }))
        .pipe(csso())
        .pipe(stripCssComments({ preserve: false }))
        .pipe(sourcemaps.write())
        .pipe(gulp.dest( styleURL ))
        .pipe(livereload())
});

gulp.task('watch', function() {
    livereload.listen();
    gulp.watch(styleWatch, gulp.series('scss'));
});