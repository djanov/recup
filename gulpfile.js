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
var Q = require('q');

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
  return gulp.src(paths).on('end', function() { console.log('start '+outputFilename)})
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
       .pipe(gulp.dest('.')).on('end', function() {console.log('end '+outputFilename)});
};

app.addScript = function(paths, outputFilename) {
    return gulp.src(paths).on('end', function() { console.log('start '+outputFilename)})
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
       .pipe(gulp.dest('.')).on('end', function() {console.log('end '+outputFilename)});
};

app.copy = function(srcFiles, outputDir){
   gulp.src(srcFiles)
       .pipe(gulp.dest(outputDir));
};

var Pipeline = function() {
    this.entries = [];
};
Pipeline.prototype.add = function() {
    this.entries.push(arguments);
};
Pipeline.prototype.run = function(callable) {
    var deferred = Q.defer();
    var i = 0;
    var entries = this.entries;
    var runNextEntry = function() {
        // see if we're all done looping
        if (typeof entries[i] === 'undefined') {
            deferred.resolve();
            return;
        }
        // pass app as this, though we should avoid using "this"
        // in those functions anyways
        callable.apply(app, entries[i]).on('end', function() {
            i++;
            runNextEntry();
        });
    };
    runNextEntry();
    return deferred.promise;
};

   gulp.task('styles', function() {
       var pipeline = new Pipeline();

   pipeline.add([
      config.bowerDir+'/bootstrap/dist/css/bootstrap.css',
      config.bowerDir+'/font-awesome/css/font-awesome.css',
      config.assetsDir+'/sass/layout.scss',
      config.assetsDir+'/sass/styles.scss'
      ], 'main.css');

       pipeline.add([
           config.assetsDir+'/sass/record.scss'
       ], 'record.css');
    pipeline.run(app.addStyle);
});

gulp.task('scripts', function() {
    var pipeline = new Pipeline();
   pipeline.add([
          config.bowerDir+'/jquery/dist/jquery.js',
          config.assetsDir+'/js/main.js'
       ], 'site.js');

    pipeline.run(app.addScript);
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
    // del.sync('web/js/*');
    del.sync('web/fonts/*');
});


gulp.task('watch', function(){
   gulp.watch(config.assetsDir+'/'+config.sassPattern, ['styles']);
   gulp.watch(config.assetsDir+'/js/**/*.js', ['scripts']);
});

gulp.task('default', ['clean','styles', 'scripts', 'fonts', 'watch']);