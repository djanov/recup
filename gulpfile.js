var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var cleanCSS = require('gulp-clean-css');
var util = require('gulp-util');
var gulpif = require('gulp-if');
var plumber = require('gulp-plumber');

var config = {
   assetsDir: 'app/Resources/assets',
   sassPattern: 'sass/**/*.scss',
   production: !!util.env.production, // Those two exclamations turn
                                     // undefined into a proper false.
   sourceMaps: !util.env.production,
   bowerDir: 'vendor/bower_components'
};
var app = {};

app.addStyle = function(paths, outputFilename) {
   gulp.src(paths)
       .pipe(gulpif(!util.env.production, plumber()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.init()))
       .pipe(sass())
       .pipe(concat(outputFilename))
       .pipe(gulpif(config.production, cleanCSS()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.write('.')))
       .pipe(gulp.dest('web/css'));
};



gulp.task('styles', function() {
   app.addStyle([
      config.bowerDir+'/bootstrap/dist/css/bootstrap.css',
      config.assetsDir+'/sass/layout.scss',
      config.assetsDir+'/sass/styles.scss'
      ], 'main.css');

   app.addStyle([
      config.assetsDir+'/sass/record.scss'
   ], 'record.css');
});

gulp.task('watch', function(){
   gulp.watch(config.assetsDir+'/'+config.sassPattern, ['styles'])
});

gulp.task('default', ['styles', 'watch']);