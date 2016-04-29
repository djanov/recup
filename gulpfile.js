var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var cleanCSS = require('gulp-clean-css');
var util = require('gulp-util');
var gulpif = require('gulp-if');
var plumber = require('gulp-plumber');
var uglify = require('gulp-uglify');
var rev = require('gulp-rev');

var del = require('del'); // This is not a gulp plugin.


var config = {
   assetsDir: 'app/Resources/assets',
   sassPattern: 'sass/**/*.scss',
   production: !!util.env.production, // Those two exclamations turn
                                     // undefined into a proper false.
   sourceMaps: !util.env.production,
   bowerDir: 'vendor/bower_components',
   revManifestPath: 'app/Resources/assets/rev-manifest.json'
};
var app = {};

app.addStyle = function(paths, outputFilename) {
   gulp.src(paths)
       .pipe(gulpif(!util.env.production, plumber()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.init()))
       .pipe(sass())
       .pipe(concat('css/'+outputFilename))
       .pipe(gulpif(config.production, cleanCSS()))
       .pipe(rev())
       .pipe(gulpif(config.sourceMaps, sourcemaps.write('.')))
       .pipe(gulp.dest('web'))
       .pipe(rev.manifest(config.revManifestPath, {
          merge: true
       }))
       .pipe(gulp.dest('.'));
};

app.addScript = function(paths, outputFilename) {
   gulp.src(paths)
       .pipe(gulpif(!util.env.production, plumber()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.init()))
       .pipe(concat('js/'+outputFilename))
       .pipe(gulpif(config.production, uglify()))
       .pipe(rev())
       .pipe(gulpif(config.sourceMaps, sourcemaps.write('.')))
       .pipe(gulp.dest('web'))
       .pipe(rev.manifest(config.revManifestPath, {
            merge: true
        }))
       .pipe(gulp.dest('.'));
};

app.copy = function(srcFiles, outputDir){
   gulp.src(srcFiles)
       .pipe(gulp.dest(outputDir));
};

   gulp.task('styles', function() {
   app.addStyle([
      config.bowerDir+'/bootstrap/dist/css/bootstrap.css',
      config.bowerDir+'/font-awesome/css/font-awesome.css',
      config.assetsDir+'/sass/layout.scss',
      config.assetsDir+'/sass/styles.scss'
      ], 'main.css');

   app.addStyle([
      config.assetsDir+'/sass/record.scss'
   ], 'record.css');
});

gulp.task('scripts', function() {
   app.addScript([
          config.bowerDir+'/jquery/dist/jquery.js',
          config.assetsDir+'/js/main.js'
       ], 'site.js');
});

gulp.task('fonts', function() {
   app.copy(
       config.bowerDir+'/font-awesome/fonts/*',
       'web/fonts'
   );
});

gulp.task('clean', function() {
    del.sync(config.revManifestPath);
    del.sync('web/css/*');
    del.sync('web/js/*');
    del.sync('web/fonts/*');
});


gulp.task('watch', function(){
   gulp.watch(config.assetsDir+'/'+config.sassPattern, ['styles']);
   gulp.watch(config.assetsDir+'/js/**/*.js', ['scripts']);
});

gulp.task('default', ['clean','styles', 'scripts', 'fonts', 'watch']);