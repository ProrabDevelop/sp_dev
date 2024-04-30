/////////////////////////////////////////////////////////
// CONFIG
/////////////////////////////////////////////////////////

var gulp = require('gulp');
var sass = require('gulp-sass')(require('sass'));
sass.compiler = require('node-sass');
var gcmq = require('gulp-group-css-media-queries');
const cleanCSS = require('gulp-clean-css');

gulp.task('scss', function () {
    return gulp.src('scss/**/*.scss')
        .pipe(sass().on('error', sass.logError))
        //.pipe(gcmq())
        //.pipe(cleanCSS({compatibility: 'edge'}))
        .pipe(gulp.dest('css'));
});

gulp.task('watch', function () {
    gulp.watch('scss/**/*.scss', gulp.series('scss'));
});
