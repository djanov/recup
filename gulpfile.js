var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('default', function() {
   gulp.src('app/Resources/assets/sass/**/*.scss')
       .pipe(sass())
       .pipe(gulp.dest('web/css'));
});