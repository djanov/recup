var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var cleanCSS = require('gulp-clean-css');

var config = {
   assetsDir: 'app/Resources/assets',
   sassPattern: 'sass/**/*.scss'
};

gulp.task('sass', function() {
   gulp.src(config.assetsDir+'/'+config.sassPattern)
       .pipe(sourcemaps.init())
       .pipe(sass())
       .pipe(concat('main.css'))
       .pipe(cleanCSS())
       .pipe(sourcemaps.write('.'))
       .pipe(gulp.dest('web/css'));
});

gulp.task('watch', function(){
   gulp.watch(config.assetsDir+'/'+config.sassPattern, ['sass'])
});

gulp.task('default', ['sass', 'watch']);