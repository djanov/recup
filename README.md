The project RecUp
=================

The RecUp is a social network for musicians...

<!--Core features(coming soon):-->
<!------------------------------->

What is [Symfony][1]
====================

> Symfony is a set of PHP Components, a Web Application framework, a Philosophy and a Community - all working together in harmony.

### Symfony [Components & Framework][2]

> Symfony is a set of components: meaning PHP libraries. Actually, it's about 30 small libraries. That means that you could use Symfony in your non-Symfony project today by using one of its little libraries. One of this little libraries is Finder: it's really good at searching deep into directories for files.

> But Symfony is also a framework where we've taken all of those components and glued them together for so that you can get things done faster.

[Installing][3] Symfony(```2.8.*```)
===================================
### Installation on Linux and Mac OS X

```
$ sudo curl -LsS https://symfony.com/installer -o /usr/local/bin/symfony
$ sudo chmod a+x /usr/local/bin/symfony
```
This gives us a new **symfony** executable, run the **symfony new** and the name of the project.

```
symfony new project_name
```

The project name is only used to determine the directory name: it's not important at all.

### Installation on Windows

First need to install [Composer][4]
-----------------------------------
After installation composer, go where the project going to be then:
```
$ composer create-project symfony/framework-standard-edition my_project_name "2.8.*"
```

### To test that Symfony is up and running

```
php bin/console server:run
```

Then go to **http:/localhost:8000** in the browser. And boom symfony is working now

Important changes:
=================

* **March 30**:
  - Changing indexAction (index.html.twig) to showAction (show.html.twig)
  - Changing {wat} to {track}

Maybe add later:
================
* **April 30**:
  - For [GULP][54] to have [autoprefixer][55](PostCSS plugin to parse CSS and add vendor
  prefixes to CSS rules).

Maj 6, 2016 (FOSUserBundle)
===========================

1.)Make new user and promote the role in console using **FOSUSerBundle** task.

2.)Overriding the **FOSUserBundle** views.

### 1. In console type:
```
php app/console fos:user:create
```
fill the username, email and password.

To promote the user for example to **ROLE_ADMIN**:

```
php app/console fos:user:promote
```
then choose the username and choose the role in this case **ROLE_ADMIN**.


###2.To override the **layout.html.twig**
First I need to set the parent bundle, in UserBundle class add a new method called **getParent**,
and set it to **FOSUserBundle** as the parent:
```
<?php
src/RecUp/UserBundle/UserBundle.php


namespace RecUp\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
```
Now I am able to override certain things inside that bundle, so I need to create the
**layout.html.twig** in the exact same directory as **FOSUserBundle**, and my template
will override the original one. The structure is **UserBundle/Resources/views/layout.html.twig**

```
UserBundle/Resources/views/layout.html.twig

{% extends '::base.html.twig' %}

{% block body %}
    {{ block('fos_user_content') }}
{% endblock %}
```
by extending the **base.html.twig** I have the override the layout, and to get the
content from the **FOSUserBundle** I added the __'fos_user_content'__ in the body block.

Now I can see the overridden **FOSUserBundle** **layout.html.twig** that has extended **default.html.twig**
and the **FOSUserBundle** content.



Maj 5, 2016 (user branch, installing FOSUserBundle)
===================================================

Making new branch for the [FOSUserBundle][57] and [EWZRecaptchaBundle][58].

Making new UserBundle for the user registration.

In RecUp make a new folder **UserBundle** and create in the root of the folder a **UserBundle.php**
file:

```
src/RecUp/UserBundle/UserBundle.php


<?php

namespace RecUp\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class UserBundle extends Bundle
{
}
```
Then in the UserBundle folder make a new folder **Entity** and a new php class **User.php**
In this file is going the Doctrine ORM User class configuration from the **FOSUserBundle**.

To make the bundle work add in the **AppKernel.php**:

```
app/AppKerlnel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            ...
            new RecUp\UserBundle\UserBundle(),
        );
    ...
...
```
The Step by step [installation FOSUserBundle][59]

Links:
------
* [FOSUserBundle][57]
* [Step by step installation FOSUserBundle][59]
* [EWZRecaptchaBundle][58]



Maj 4, 2016
===========

Making the plan for the page structure and the page layout on paper.

The pages done:
   * home index(1)
   * register(3)
   * user profile (4)


Maj 3, 2016 (frontend: genre(Alice), index page)
================================================

Making a custom function to generate random genres for the songs.

First in the **LoadFixtures.php** make a new public function called **genres()** and make a list
of the genres you want, after that make it to return a random one every time:

```
src/RecUp/RecordBundle/DataFixtures/ORM/LoadFixtures.php

 ...
 public function genres()
    {
        $genre = [
            'Classical',
            'Experimental',
            'Flamenco',
            ...
        ];

        $key = array_rand($genre);

        return $genre[$key];
    }
```
Then after that use the new function in the Alice **fixtures.yml**:

```
src/RecUp/RecordBundle/DataFixtures/ORM/fixtures.yml

RecUp\RecordBundle\Entity\Record:
  record_{1..10}:
    songName: <songs()>
    artist: <text(15)>
    genre: <genres()>
    about: <sentence()>
    isPublished: <boolean(75)>
    ...
```
After that make the new fixtures:

```
php app/console doctrine:fixtures:load
```

### Making the index page

First need to create a new file in **RecUp/Resources/Views/Default/index.html.twig**
And extend the base twig template:

```
RecUp/Resources/Views/Default/index.html.twig

{% extends 'base.html.twig' %}
```

Next open the **DefaultController.php** and add a new action return the **index.html.twig**
template and add the route in the annotation:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

...
class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('@Record/Default/index.html.twig');
    }

    ...
```
This will render the twig template file.

The **"/"** means that this will match the homepage (/).



Maj 2, 2016 (DI)
================

Understanding dependency injection.

```
app/config/services/yml

twig_asset_version_extension:
        class: RecUp\RecordBundle\Twig\AssetVersionExtension
        arguments: ["%kernel.root_dir%"]
        tags:
          - { name: twig.extension }
```

This twig extension is going pass an arguments for the **["%kernel.root_dir%"]**, and that
means this will have the root of the project(the path) for the class in **RecUp\RecordBundle\Twig\AssetVersionExtension**.
And in the twig extension (AssetVersionExtension):

```
src/RecUp/RecordBundle/Twig/AssetVersionExtension.php

...
class AssetVersionExtension extends \Twig_Extension
{
    private $appDir;

    public function __construct($appDir)
    {
        $this->appDir = $appDir;
    }
...
```
The __construct method have an argument **$appDir** and this will find the root of the project
that I added in the services, because its going to see the root path.

If i have two or more arguments in the services, then in the __construct method the argument name can
can be anything but need to be called in the order it was in the services for example:

```
app/config/services/yml
...
services:
     app.markdown_transformer:
         class: RecUp\RecordBundle\Service\MarkdownTransformer
         arguments: ['@markdown.parser', '@doctrine_cache.providers.my_markdown_cache']
...
```
This have two arguments, the **@** means that those are services. And in the **MarkdownTransformer.php**
service:

```
src/RecUp/RecordBundle/Service/MarkdownTransformer.php

...
class MarkdownTransformer
{
    private $markdownParser;

    private $cache;

    public function __construct(MarkdownParserInterface $markdownParser,Cache $cache)
    {
        $this->markdownParser = $markdownParser;
        $this->cache = $cache;
    }
...
```

There is the first argument called by **$markdownParser** and the second one **$cache**, so that in the
services.yml the arguments are in the order.

Documentation about [Types of Injection][56]

The most common is the Constructor Injection, I can use Type hinting like in the
```public function __construct(MarkdownParserInterface $markdownParser,Cache $cache)``` example
this means that I can be sure that a suitable dependency has been injected. By type-hinting,
I will get a clear error immediately if an unsuitable dependency is injected. By type hinting
using an interface rather than a class I can make the choice of dependency more flexible.
And if I use only methods defined in the interface, I can gain the flexibility and still safely
use the object.

Links:
------
* [Types of Injection][56]



Maj 1, 2016 (gulp: fixing the task order)
=========================================

##### There is no order to Dependent tasks

If you're dependent on a task like **fonts*, that task must return a Promise or a Gulp stream.
If it doesn't, Gulp actually has no idea when **fonts** finishes.

So it just runs **watch** right awy. So, **return app.copy** from the **font** task, since
**app.copy** returns Gulp stream.

```
gulp.task('fonts', function() {
  return  app.copy(
          config.bowerDir+'/font-awesome/fonts/*',
          'web/fonts'
        ).on('end', function() {console.log('finished fonts!')});
});
```

Now, Gulp can know when **fonts** truly finishes it work. Now its there **fonts** finishes, and
then **watch** starts. And there's one more thing: Gulp finally prints "Finished 'fonts'" in the
right place, after **fonts** does it work.

Its because Gulp can't report when a task finishes unless that task returns a Promise or a Gulp
stream. This means it should return one of these from every task.

Lastly we should always return a stream or promise, in the **styles** it doesn't have a single
stream - it has two that are combined into the pipeline. So we need to wait until both of them
are finished. And its just **return pipeline.run()**:
```
gulpfile.js


gulp.task('styles', function() {
    var pipeline = new Pipeline();

    return pipeline.run(app.addStyle);
});

gulp.task('scripts', function() {
    var pipeline = new Pipeline();

    return pipeline.run(app.addScript);
});
```
This isn't magic, knpuniversity wrote the Pipeline code, and the **run()** method return a
 Promise that resolves once everything is done.



April 30, 2016 (gulp: fixing the css orders)
============================================

If i run **gulp**, gulp shows that everything is in order: clean start, clean finish. But
that's wrong. The truth is that everything happening all at once, asynchronously. Gulp has no idea
when each task actually finishes, so I need to fix that.

Gulp streams ar like a promise each line in a gulp stream is asynchronous - like an AJAX call.
This means that before **gulp.src()** finishes, the next **pipe()** is already being called.
But I need each line to run in order, so when I call **pipe()**, it doesn't run what's inside
immediately: it schedules it to be called once the previous line finishes. The effect is like
making an AJAX call, adding a success listener, then making another AJAX call from inside it.

So does the **main.css** file finish compiling before **record.css** starts? Does the scripts
wait for the styles task for finish? To find out add the **on('end')** listeners.
Like with AJAX, each line returns something that acts like a Promise. That means, for any line, I
can write **on** to add a listener for when this specific line actually finishes. When that
happens, add **console.log('start '+filename)**. And add another listener to the last line,
but change the text to "end":

```
gulpfile.js
    ...
app.addStyle = function(paths, outputFilename) {
    gulp.src(paths).on('end', function() { console.log('start '+outputFilename)})
    ...
        .pipe(gulp.dest('.')).on('end', function() { console.log('end '+outputFilename)})
};
    ...
```
Run **gulp**, it said it finished "styles", but it really means it was done executing the
**styles** task. But things finish way later. In fact they don't start the process until later.
And interestingly the **record.css** starts before **main.css**, even though main is the
first style I add.

##### Using the Pipeline

To make things work in order, and if I have 10 css files, to don't use 10 levels of nested
listeners, use object that was created by knpuniversity called Pipeline, it has a dependency
on an object called [q][53], so let's go install that:

```
npm install q --save-dev
```
On top, add the **require**:

```
gulpfile.js

var gulp = require('gulp');
...
var Q = require('q');
...
```

The Pipeline object:

```
gulpfile.js

...
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
...
```

To use it, create a **pipeline** variable and set it to **new Pipeline()**. Now instead of
calling **app.addStyle()** directly, call **pipeline.add()** with the same arguments.
**pipeline.add()** is basically queuing those to be run. So at the end, call **pipeline.run()**
and pass it the actual function it should call:

```
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
```
To work this peace of the code I need to return the the stream from **addStyle**:

```
gulpfile.js
...
app.addStyle = function(paths, outputFilename) {
   return gulp.src(paths).on('end', function() { console.log('start '+outputFilename)})
   ...
```
The console.log is for debugging purpose for the command prompt. Now if I run **gulp**
Behind the scenes, the Pipeline is call **addStyle**, waiting until it finishes, then calling
**addStyle** again.

##### Pipelining scripts

Now do the same for the scripts task so for the JS files. First make sure tu actually return from
**addScript** - we need that stream so the **Pipeline** can add an **end** listener:

```
gulpfile.js
...
app.addScript = function(paths, outputFilename) {
    return gulp.src(paths).on('end', function() { console.log('start '+outputFilename)})
    ...
```
Then, in the **scripts** create the **pipeline** variable, then **pipeline.add()**. And
**pipeline.run()** in the bottom to finish:

```
gulpfile.js


gulp.task('scripts', function() {
    var pipeline = new Pipeline();
   pipeline.add([
          config.bowerDir+'/jquery/dist/jquery.js',
          config.assetsDir+'/js/main.js'
       ], 'site.js');

    pipeline.run(app.addScript);
});
```
And run **gulp**.

Remember that Gulp returns everything all at once, but it is possible to make one entire task
wait for another to finish, more that later.

Links:
------
* [q][53]



April 29, 2016 (gulp: del, javascript versioning)
=================================================

After a while, the versioning is going to clutter up the **web/css** directory. To prevent that
use the library called [del][52]:
```
npm install del --save-dev
```
This is not a gulp plugin, add at the top the **require**:

```
gulpfile.js

var gulp = require('gulp');
...
var del = require('del');

This library helps delete files. I need a way to clean up the generated files. Add a new task,
and call it **clean**:

```
```
gulpfile.js
...
gulp.task('clean', function() {

});
...
```
Inside here, remove everything that gulp generates. The first thing is the **rev-manifest.json** file.
I don't need to clear this, but if I delete a CSS file, its last map value will still live here.
So keep only the real files in this list.

To do that use **dell.sync()**. This means that our code will wait at this line until the file is
actually deleted. The path to the manifest file is used by the full path so make it a new config
option **revManifestPath**:

```
gulpfile.js
...
var config = {
    ...
    revManifestPath: 'app/Resources/assets/rev-manifest.json'
};
    ...
    gulp.src(paths)
     ...
        .pipe(rev.manifest(config.revManifestPath, {
            merge: true
        }))
    ...
```
Now just feed that to **del.sync()**. The other things is need to clean up are __web/css/*__,
__web/js/*__ and __web/fonts/*__:

```
gulpfile.js
...
gulp.task('clean', function() {
    del.sync(config.revManifestPath);
    del.sync('web/css/*');
    del.sync('web/js/*');
    del.sync('web/fonts/*');
});
...
```
Now add this to the beginning of the **default** task. So when the gul starts its going to clean
up things.
```
gulpfile.js

gulp.task('default', ['clean', 'styles', 'scripts', 'fonts', 'watch']);
```

### Versioning JavaScript

The **site.js** file need versioning too. To make that steal the **rev()** line from **addStyle()**
land put that right before the sourcemaps of **addScript**, and the 2 lines that dump the manifest file.
Finnish with correcting the paths to the manifest has the **js/** directory part in the filename.
So, to **concat()**, add **js/**, then just push **web** from the first **dest()** call:
```
gulpfile.js
...
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
       ...
```
And before restarting gulp add the **asset_version** to the **site.js** in the **show.html.twig**

```
src/RecUp/RecordBundle/Resources/views/Default/show.html.twig

{%  block javascripts %}
{{ parent() }}
    ...
    <script src="{{ asset('js/site.js'|asset_version) }}"></script>
    ...
{% endblock %}
```

Links:
-----
* [del library][52]



April 28, 2016 (gulp: cache busting)
====================================

If I deploy an update to **main.css**, the visitors will need to clear cache to see the new stuff.
So i need a plugin that will do that for me the cache busting.

The plugin for this is called [gulp-rev][51], rev means "revision". In the docs, the plugin
does one thing: I point it at a file - like **main.css** and it changes the name, adding a hash
on the end. The hash is based on the contents, sp it'll change whenever the file is changes.

The task is here to make somehow the template automatically point to whatever the latest hashed
filename is, then I have cache-busting.

First download the plugin:

```
npm install --save-dev gulp-rev
```
Then insert the require line:

```
gulpfile.js
var gulp = require('gulp');
...
var rev = require('gulp-rev');
```

Next in **addStyle**, add the new **pipe()** right before the sourcemaps are dumped so that
both the CSS and its map are renamed. Inside, use **rev()**, then remove the public CSS folder
in **web**, and run **gulp**. Now the two css filename has changed to: **main-9d28056084.css** and
**record-8d66f128c9.css** and the maps also got renamed. The site now is broken, because
I have including the old **main.css** file in the layout.

##### Dumping the rev-manifest.json File

I can't just update **base.html.twig** to use the new hashed name because it would re-break
every time the file is changed. I need a map to say **main.css** is called **main-9d28056084.css**.
If i had that i can use it inside with PHP code to rewrite the **main.css** in the base template
to hashed version automatically. When the hashed name updates, the map would update, and so my code.

And the **gulp-rev** plugin have this. The map is called a "manifest". To get **gulp-rev** to
create that, I need to add another **pipe()** in the end to **rev.manifest()** and tell where I
want the manifest file:

```
gulpfile.js

...
app.addStyle = function(paths, outputFilename) {
   gulp.src(paths)
       .pipe(gulpif(!util.env.production, plumber()))
    ...
       .pipe(rev.manifest('app/Resources/assets/rev-manifest.json'))
       .pipe(gulp.dest('.'));
};
```
The file doesn't need to be publicly accessible, the PHP code just needs to be able to read.
There's now multiple **gulp.dest()**, So fore I've always had one **gulp.src()** at the top
and one **gulp.dest()** at the bottom but I can have more. In the **addStyle** the first
**gulp.dest()** writes the CSS file, but once I pipe to **rev.manifest()**, the gulp stream
changes, instead of being the CSS file, the manifest is now being passed through the pipes. So
the last **gulp.dest()** just writes that file relative to the root directory.

Run **gulp** and there is the **rev-manifest.json** file. It holds the map from **main.css**,
but it missing **record.css** to fix that add a **merge: true** to the **rev.manifest**

```
gulpfile.js
    gulp.src(paths)
        ...
        // write the rev-manifest.json file for gulp-rev
        .pipe(rev.manifest('app/Resources/assets/rev-manifest.json', {
            merge: true
        }))
    ...
```

There is one more problem, to make this in **css/main.css**. To fix that inside **concat()**,
update it to **css/** then the filename. That changes the filename that's inside the Gulp stream.
To keep the file in the sam spot, just take the **css/** out of the **gulp.dest()** call:

```
gulpfile.js
..
app.addStyle = function(paths, outputFilename) {
   gulp.src(paths)
        ...
       .pipe(concat('css/'+outputFilename))
        ...
       .pipe(rev())
       .pipe(gulpif(config.sourceMaps, sourcemaps.write('.')))
       .pipe(gulp.dest('web'))
       .pipe(rev.manifest('app/Resources/assets/rev-manifest.json', {
          merge: true
       }))
       .pipe(gulp.dest('.'));
};
        ...
```

Now the **rev-manifest.json** has the **css/** prefix:

```
app/Resources/assets/rev-manifest.json

{
  "css/main.css": "css/main-0710b93ea3.css",
  "css/record.css": "css/record-8d66f128c9.css"
}
```

##### Making the link href Dynamic

For that I need to create an Twig extension file(this is an empty twig extension file to get started):

```
<?php
src/RecUp/RecordBundle/Twig/AssetVersionExtension.php


namespace RecUp\RecordBundle\Twig;

class AssetVersionExtension extends \Twig_Extension
{
    private $appDir;

    public function __construct($appDir)
    {
        $this->appDir = $appDir;
    }

    public function getFilters()
    {
        return array(

        );
    }

    public function getName()
    {
        return 'asset_version';
    }
}

```
And to Twig to know about this add in services the extension:

```
app/config/srvices.yml
...
services:
    twig_asset_version_extension:
        class: AppBundle\Twig\AssetVersionExtension
        arguments: ["%kernel.root_dir%"]
        tags:
            - { name: twig.extension }

```
This Twig extension is now ready to go, all I need to do is register the **asset_version** filter
this name can be anything I just chose this unique name for the filter to use later in the Twig
template. This filter is will go inside **getFilter()** with **new \Twig_SimpleFilter('asset_version', ...)**
and it will call a method in this class called **getAssertVersion**:

```
src/RecUp/RecordBundle/Twig/AssetVersionExtension.php
    ...
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('asset_version', array($this, 'getAssetVersion')),
        );
    }
    ...
```

Below, add that function. It'll be passed the **$filename** that I try to version. So for me,
**css/main.css**.

The task is: open up **rev-manifest.json**, find the path, then return its versioned filename value.
The path to that file is **$this->appDir** this property is already set up to point to the **app/**
directory then **Resources/assets/rev-manifest.json**:

```
<?php
src/RecUp/RecordBundle/Twig/AssetVersionExtension.php


namespace RecUp\RecordBundle\Twig;

class AssetVersionExtension extends \Twig_Extension
{
    private $appDir;

    public function __construct($appDir)
    {
        $this->appDir = $appDir;
    }

    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('asset_version', array($this, 'getAssetVersion')),
        );
    }

    public function getAssetVersion($filename)
    {
        $manifestPath = $this->appDir.'/Resources/assets/rev-manifest.json';

    }
    ...
```

Now make some clear exceptions. If the file is missing, open it up, decode the JSON, and set the map
 to an **$assets** variable. Since the manifest file has the original filename as the key. let's
 throw one more exception if the file isn't in the map. And finally, return the mapped value:


 ```
 <?php
 src/RecUp/RecordBundle/Twig/AssetVersionExtension.php


namespace RecUp\RecordBundle\Twig;

 class AssetVersionExtension extends \Twig_Extension
 {
     private $appDir;

     public function __construct($appDir)
     {
         $this->appDir = $appDir;
     }

     public function getFilters()
     {
         return array(
             new \Twig_SimpleFilter('asset_version', array($this, 'getAssetVersion')),
         );
     }

     public function getAssetVersion($filename)
     {
         $manifestPath = $this->appDir.'/Resources/assets/rev-manifest.json';
         if (!file_exists($manifestPath)) {
             throw new \Exception(sprintf('Cannot find manifest file: "%s"', $manifestPath));
         }

         $paths = json_decode(file_get_contents($manifestPath), true);
         if (!isset($paths[$filename])) {
             throw new \Exception(sprintf('There is no file "%s" in the version manifest!', $filename));
         }

         return $paths[$filename];
     }
 ```
So I give it **css/main.css** and it gives us the hashed filename.

Now add the filter to the **base.html.twig** and the **show.html.twig**:

```
app/Resources/views/base.html.twig
    ...
        {% block stylesheets %}
            <link rel="stylesheet" href="{{ asset('css/main.css'|asset_version) }}">
        {% endblock %}
    ...
```
```
src/RecUp/RecordBundle/Resources/views/Default/show.html.twig

 {% block stylesheets %}
     {{ parent() }}
     <link rel="stylesheet" href="{{ asset('css/record.css'|asset_version) }}"/>
 {% endblock %}
```

Refresh, the site is back, the hashed filename shows up in the source.

If I change anything for example in the **layout.scss** the gulp watch robots are working, so
I immediately see a brand new hashed **main.css** file in **web/css**. And the if i refresh the page
I can see that the layout automatically updated the new filename, the new CSS filename pops up
and the changes are working.

**Don't commit the manifest**, it's generated automatically by Gulp. So add it to the **.gitignore** file.

```
.gitignore
...
/app/Resources/assets/rev-manifest.json
```

Links:
-----
* [gulp-rev][51]



April 27, 2016 (gulp: font awesome)
===================================

To use font-awesome it's not enough, to add the file to the **main.css**:
```
gulpfile.js
    ...
   app.addStyle([
      config.bowerDir+'/bootstrap/dist/css/bootstrap.css',
      config.bowerDir+'/font-awesome/css/font-awesome.css',
      config.assetsDir+'/sass/layout.scss',
      config.assetsDir+'/sass/styles.scss'
      ], 'main.css');
   ...
```
When I refresh, I got 404 errors for the FontAwesome font files. Font Awesome goes up one level
from its CSS and looks for a **fonts/** directory. Since its code lives in **main.css**, it goes up
one level and looks for **fonts/** right at the root of **web/**. I can control this with
Font Awesome Sass package, to control where it's looking, but even then I have the problem.
The FontAwesome **fonts/** directory is deep inside **vendor/bower_components**. I need to copy this stuff
into **web/**.

##### The copy function

Copying function is easy to do, so go straight to making a new function **app.copy** with two
arguments **srcFiles** and **outputDir**. It will read some source files and copy them to the new
spot. To copy files in Gulp, just create the normal pipe chain, but without any filters in the
middle: **gulp.src(srcFiles)**, then pipe that directly to **gulp.dest(outputDir)**

```
gulpfile.js
    ...
app.copy = function(srcFiles, outputDir) {
    gulp.src(srcFiles)
        .pipe(gulp.dest(outputDir));
};
    ...
```

##### Make the Fonts public

Add a new task called **fonts**. The job of this task will be to "publish" any fonts that I have into
**web/**. Right now, it's just the FontAwesome. Use the **app.copy()** and for the path start with
**config.bowerDir** then the __font-awesome/fonts/*__ path, to grab everything. For the target, just
**web/fonts**:

```
gulpfile.js

    ...
gulp.task('fonts', function() {
   app.copy(
       config.bowerDir+'/font-awesome/fonts/*',
       'web/fonts'
   );
});
    ...
```

Add this to run with the default task:

```
gulpfile.js

    ...
gulp.task('default', ['styles', 'scripts', 'fonts', 'watch']);
```

But don't add to watch, its not going to be actively changing.

Restart gulp, and its running the **fonts** task, inside **web/**, I have a new **fonts/** directory.
And since FontAwesome is looking right here for them, the 404 error is gone.

Don't commit the fonts, the **web/fonts** directory is generated file so don't commit like the **css/**
and **js/** folders:

```
.gitignore
...
/web/fonts
```



April 26, 2016 (gulp: bower bootstrap file in main.css, minify and combine js)
=============================================================================

First add a new config variable set it to **vendor/bower_components**:

```
gulpfile.js
...
var config = {
    ...
    bowerDir: 'vendor/bower_components'
};
...
```

add this to the **addStyle** function:

```
gulpfile.js
...
gulp.task('styles', function() {
    app.addStyle([
        config.bowerDir+'/bootstrap/dist/css/bootstrap.css',
        config.assetsDir+'/sass/layout.scss',
        config.assetsDir+'/sass/styles.scss'
    ], 'main.css');
...
});
...
```
And I don't need to worry about getting the min file, because I already taking care of that.
This file will go through the **sass** filter, but that's ok. It'll just look like the most
boring Sass file ever.

Run gulp, and now in the browser **main.css** starts with a Bootstrap code. And if i run
gulp with **--production** flag it would be minified.

### Minify and Combine JavaScript

First create a **js** directory inside **app/Resources/assets** and a new file, call **main.js**.

Make some dummy jquery, to add something visual to the footer:

```
app/Resources/assets/js/main.js

$(document).ready(function() {
    console.log('It\'s a Unix system, I know this');
    $('.footer').prepend('<span>Life finds a way -> </span>');
});
```
The **main.js** isn't in a public directory so Gulp for help.
Create a new Task called **scripts**, inside here its going to do the exact same stuff I have
for the CSS. Copy the inside of **addStyle** and paste it in this task. Delete **sass** filter
and **cleanCSS**. In **src()**, start with **config.assetsDir** then **/js/main.js**. For jQuery,
above **main.js** add **config.bowerDir** then **/jquery/dist/jquery.js**. And put in **dest()**
to **web/js** and give **concat()** a filename **site.js**:

```
gulpfile.js
...
gulp.task('scripts', function() {
    gulp.src([
        config.bowerDir+'/jquery/dist/jquery.js',
        config.assetsDir+'/js/main.js'
      ])
       .pipe(gulpif(!util.env.production, plumber()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.init()))
       .pipe(concat('site.js'))
       .pipe(gulpif(config.sourceMaps, sourcemaps.write('.')))
       .pipe(gulp.dest('web/js'));
});
...
```
Run **gulp scripts**, now in **web/**, we have a new **site.js** file and its map.

##### Updating watch and default

Update the **watch** task, Copy the existing line, for path look for anything in the **js**
directory recursively  ```**js/**/*.js```, and when that happens, run **scripts**:

```
gulpfile.js
...
gulp.task('watch', function(){
   gulp.watch(config.assetsDir+'/'+config.sassPattern, ['styles']);
   gulp.watch(config.assetsDir+'/js/**/*.js', ['scripts']);
});
gulp.task('default', ['styles', 'scripts', 'watch']);
```
Add this to the **default** task too. Run **gulp**, it runs **scripts** and then watch waits.

After all that, I have the real **site.js** file in a public directory. Using the **asset()**
function from Symfony add the path:

```
src/RecUp/Resources/views/Default/show.html.twig
        ...
        {% block javascripts %}
        ...
            <script src="{{ asset('js/site.js') }}"></script>
        {% endblock %}
        ...
```
Refresh and the quote is now in the footer ``Life finds a way ->``.
This is a generated file, so add it to the **.gitignore**:
```
.gitignore

...
/web/js
```

##### Minify with gulp-uglify

To minify JS-files, I use the [gulp-uglify][50] plugin. First download using npm:

```
npm install --save-dev gulp-uglify
```
Add the **require** line to the **gulpfile.js**

```
gulpfile.js

var gulp = require('gulp');
...
var uglify = require('gulp-uglify');
...
```

Copy the **CleanCSS()** line from the **addStyle**, so I have the **--production** flag behavior.
Paste it and change to **uglify()**:

```
gulpfile.js
    ...
gulp.task('scripts', function() {
    gulp.src([
        config.bowerDir+'/jquery/dist/jquery.js',
        config.assetsDir+'/js/main.js'
      ])
       .pipe(gulpif(!util.env.production, plumber()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.init()))
       .pipe(concat('site.js'))
       .pipe(gulpif(config.production, uglify()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.write('.')))
       .pipe(gulp.dest('web/js'));
});
    ...
```
Now with **gulp** it use the non-minified version, but with **gulp --production** now I have the
file uglified.

##### Multiple JavaScript Files

I need not just one js file, I need page-specific js files, so to do that add a new **app.addScript**
function with **paths** and **filename** arguments to be dynamic:

```
gulpfile.js

    ...
app.addScript = function(paths, outputFilename) {
   gulp.src(paths)
       .pipe(gulpif(!util.env.production, plumber()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.init()))
       .pipe(concat(outputFilename))
       .pipe(gulpif(config.production, uglify()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.write('.')))
       .pipe(gulp.dest('web/js'));
};
    ...
```
Back in the **scripts** task, just call the function and pass those paths, then pass filename **site.js**:

```
gulp.task('scripts', function() {
   app.addScript([
          config.bowerDir+'/jquery/dist/jquery.js',
          config.assetsDir+'/js/main.js'
       ], 'site.js');
});
```

Delete the previously generated **site.js** in the public directory **web/js/site.js**. And run gulp.

Now if i need a page-specific JavaScript file, I'll just add another **addScript** call here.

Links:
------
* [gulp-uglify for JS to minify][50]



April 25, 2016 (gulp: page specific css)
========================================

To make page specific css, make new **record.sccs** file in **app/Resources/assets/sass/**
, after some page specific styling, configure Gulp to give two files: **main.css** made from
**styles.scss** and **layout.scss** and **record.css** made from the **record.scss**, then include
**record.css** only on the show page.

##### Including specific Files in main.css

First, make **main.css** only include two of these files. Update **gulp.src()**. Instead of a pattern,
pass an array:

```
gulpfile.js

...
gulp.task('sass', function() {
    gulp.src([
        config.assetsDir+'/sass/layout.scss',
        config.assetsDir+'/sass/styles.scss'
    ])
        .pipe(plugins.plumber())
        ...
```

##### Isolating the Styles pipeline

To make **record.css** seperate to the **main.css** I need to create an **app** class, and I will
 store custom function here. Create **addStyle**, give it two arguments the **paths** to process and
 a final **filename** to write. Next, copy the guts of the **sass** task into **addStyle**
 and make it dynamic. fill in **paths** on top, and **filename** instead of **main.css**:

 ```
 gulpfile.js
 ...
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
 ...
 ```
 Now In the **sass* task, call **app.addStyle()**, keep the two paths, comma, then **main.css**:

 ```
 gulpfile.js
    ...
gulp.task('sass', function() {
   app.addStyle([
      config.assetsDir+'/sass/layout.scss',
      config.assetsDir+'/sass/styles.scss'
      ], 'main.css');
    });
    ...
 ```

##### Processing a Second CSS File

To do that, just call **addStyle()** again. and make it oad only **record.scss**. And give a different
output name **record.css** but this can be anything.

```
gulpfile.js
...
gulp.task('sass', function() {
   ...
   app.addStyle([
      config.assetsDir+'/sass/record.scss'
   ], 'record.css');
});
   ...
```

##### Updating the Template

The last step is to add a **link** tag to this one page. In twig override the **stylesheets**, call
the **parent()** function to keep what's in the layout, then create a normal **link** tag that
points to **css/record.css**:

```
src/RecUp/RecordBundle/Resources/views/Default/show.html.twig
...
{% block stylesheets %}
    {{ parent() }}
    <link rel="stylesheet" href="{{ asset('css/record.css') }}"/>
{% endblock %}
...
```
Run gulp, and refresh the page, now I have a page-specific CSS. So when if I need to add some new CSS
don't need to throw in the one gigantic main CSS file, if I need something page-specific, I can
now compile a new CSS file just for that.



April 24, 2016 (gulp)
=====================
GULP: sourcemaps only in development, plumber.
----------------------------------------------

I have now the **--production** so that everything is minified when I deploy. But when i do
that i don't want the sourcemaps to be there anymore, for that purpose to note share my
source files.

So I need only to run sourcemaps when I'm not in production. First add another config value
**sourceMaps** and set it to be not **production**:

```
gulpfile.js
...
var config = {
       assetsDir: 'app/Resources/assets',
       sassPattern: 'sass/**/*.scss',
       production: !!util.env.production,
       sourceMaps: !util.env.production
};
...
```

Don't use anymore **util.noop**, Use the plugin [gulp-if][46], install it:

```
npm install gulp-if --save-dev
```

Add the **require** line:

```
gulpfile.js

var gulp = require('gulp');
...
var gulpif = require('gulp-if');
...
```

This plugin is to help with the fact that we can't break up the pipe chain with if statements.
With it, you can add **gulpif()** inside **pipe()**. The first argument is the condition to test
so **config.sourceMaps**. And if that's true, we'll call **sourcemaps.init()**. Do the same
thing down for **sourcemaps.write**, and change the **CleanCSS()** to this approach:

```
gulpfile.js
...
gulp.task('sass', function() {
   gulp.src(config.assetsDir+'/'+config.sassPattern)
       .pipe(gulpif(config.sourceMaps, sourcemaps.init()))
        ...
       .pipe(gulpif(config.production, cleanCSS()))
       .pipe(gulpif(config.sourceMaps, sourcemaps.write('.')))
       .pipe(gulp.dest('web/css'));
});
...
```
Now if I run **gulp** I see the non-minified version with sourcemaps. But if i run
 **gulp --production** then no more sourcemaps, but minified version.

**Notice**: When I run just gulp then the value of the config.sourceMaps will be true because one **!**(negation)
turns undefined (util.env.production -> this is undefined by default) to true so the gulpif will run on the sourcemaps init and write,
but the config.production is false because of 2 **!**(negation) so the minifying will not going to be executed.

If I run gulp with --production the config.sourceMaps going to be now false because if negate true i get false,
and the sourcemaps init and write will not going to be executed, but the minifying will be executed because
the config.production false will became true. [More about negation and double negation][47].


### Plumber

With **gulp** running, when I update a file, gulp-bots recompile CSS. But if I make a syntax error
for example in **layout.scc**, then **gulp** will explode and will not run anymore. So if even if
I fix the error **gulp** is dead.

For this problem is another plugin the [gulp-plumber][49]:
```
npm install --save-dev gulp-plumber
```
Add the **require**:

```
gulpfile.js

var gulp = require('gulp');
...
var plumber = require('gulp-plumber');
...
```

And now I need to pipe gulp through this plugin before any logic that might cause an error. So right
after **gulp.src**:

```
gulpfile.js

    ...
    gulp.src(config.assetsDir+'/'+config.sassPattern)
        .pipe(gulpif(!util.env.production, plumber()))
        ...
        .pipe(gulp.dest('web/css'));
    ...
```
**Notice**: ```.pipe(gulpif(!util.env.production, plumber()))``` Plumber prevents gulp from throwing
a proper error exit code. When building for production I want a proper error, so use **plummber()**
only in development And that's it, now if i mess up in **layout.scss** gulp does show the error,
but doesn't die anymore.

Links:
------
* [gulp-if][46]
* [More about negation and double negation][47]
* [gulp-cheatsheet][48]
* [gulp-plumber][49]



April 23, 2016 (gulp)
=====================
GULP: watch task for changes, concat files, Minify css, Minify only in production.
----------------------------------------------------------------------------------
Gulp comes native with **watch()** function. I going to use this function for watching the
Sass file whenever is change.
First configure the variables, create a **config** variable and store the paths in here:

```
gulpfile.js
...
var config = {
    assetsDir: 'app/Resources/assets',
    sassPattern: 'sass/**/*.scss'
};
...
```
Then create a second task, by using **gulp.task()**.
Move the **default** task into this new one. Make the default task to run the **sass** task.
To do this, replace the function callback with an array of task names:

```
...
gulp.task('sass', function() {
   gulp.src(config.assetsDir+'/'+config.sassPattern)
       .pipe(sourcemaps.init())
       .pipe(sass())
       .pipe(sourcemaps.write('.'))
       .pipe(gulp.dest('web/css'));
});

gulp.task('default', ['sass']);
```
We can have a third argument, Gulp will run all the "dependent tasks" first, then call my function.
So if i run **gulp**, it runs the **sass** task first.

Adding the watch task

Copy from config variable the paths and pass it to **watch()**. This tells it to watch for changes
in any of these files. The moment it sees something, we want it to re-run the **sass** task.
So put that as the second argument:

```
gulpfile.js
...
gulp.task('watch', function(){
   gulp.watch(config.assetsDir+'/'+config.sassPattern, ['sass'])
});
...
```
Now if I run **gulp watch** then its waiting for me the change something, and if i change the
background color to black in **styles.scc** the watch function will call the **sass** task,
so it will generate the new css file in the public directory. To make the **default** task
useful so that whenever we start working on a project, we'll want to run the **sass** task to
initially process things and then **watch** for watching changes. Add **watch** to the array
to do that, and now its enough to run just **gulp** it will run the **sass** first and then starts
watching for changes:

```
gulp.task('default', ['sass', 'watch']);
```

### Concat files

I made a new **layout.scss** file, when i run the **gulp** the **watch** task running in the
background, and it's looking for any **.scss** file in that directory. So when i check in the
public css folder i see now the **layout.css** and the **layout.css.map** file, but the
file is not accessed because, I need first add the **link** tag in the base template.

But now the users download a lot of CSS files. I need to combine them all into a singel fiel.
There's a plugin for that, the [gulp-conat][43]. install via nmp:

```
npm install gulp-concat --save-dev
```
Add the **require** statement:

```
gulpfile.js

var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
...
```

The **gulp.src()** function loads a many files. The **concat()** function combines those into
just 1 file. Right after **sass()** - **pipe()**, then **concat()**. And pass the filename
**main.css**:

```
gulpfile.js
...
gulp.task('sass', function() {
   gulp.src(config.assetsDir+'/'+config.sassPattern)
       .pipe(sourcemaps.init())
       .pipe(sass())
       .pipe(concat('main.css'))
       .pipe(sourcemaps.write('.'))
       .pipe(gulp.dest('web/css'));
});
...
```
Be careful to keep between the **sourcemaps** lines because we're smashing multiple files
into one. and that'll change the line numbers and source files for everything. But
sourcemaps will kep track of all of that for us.

Delete the **web/css** css directory before testing out, then run **gulp**. Now it's
create only one **main.css** file and its map file. And it's got the CSS from both source
files. Go back and change the base template to only link to this one css file:
```
app/Resources/views/base.html.twig
    ...
        {% block stylesheets %}
            <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
            <link rel="stylesheet" href="{{ asset('css/main.css') }}">
            <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/font-awesome.min.css') }}">
        {% endblock %}
    ...
```
Refresh and now just one CSS file. other then the bootstrap and font-awesome. And if I check
the sourcemap still works, in inspect elements is still using the **style.sccs** and the **layout.scss**.

### Minify css

I have multiple file into one, but the **main.css** has a lot of whitespaces. The answer is Minify.
The plugin for Minfy CSS is [gulp-clean-css][44]. download using npm:

```
npm install gulp-clean-css --save-dev
```

Add the require line:

```
gulpfile.js

var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
var concat = require('gulp-concat');
var cleanCSS = require('gulp-clean-css');
```

Put the **cleanCSS()** after the concat using the **pipe()** function.

```
gulpfile.js
...
gulp.task('sass', function() {
   gulp.src(config.assetsDir+'/'+config.sassPattern)
       .pipe(sourcemaps.init())
       .pipe(sass())
       .pipe(concat('main.css'))
       .pipe(cleanCSS())
       .pipe(sourcemaps.write('.'))
       .pipe(gulp.dest('web/css'));
});
...
```
Run gulp, and now **main.css** is a single line. But with the power of the sourcemaps, we still
get the correct **style.sccs** line in the inspector.

### Minify only in Production

Minify is make sens in deploying, but locally is better to keep the whitespaces for debugging.
The goal is when i run **gulp**, i don't want minify, but if i run **gulp --production** I do want
it to minify

Install [gulp-util][45] plugin for that:

```
npm install gulp-util --save-dev
```
Next, grab the **require** line and past it in the top:

```
gulpfile.js

var gulp = require('gulp');
...
var util = require('gulp-util');
...
```
Add a config value called **production** and set that to **!!util.env.production**:

```
gulpfile.js

var config = {
    assetsDir: 'app/Resources/assets',
    sassPattern: 'sass/**/*.scss',
    production: !!util.env.production
};
```
The two exclamations turn **undefined** into proper false.

Now I need some **if** logic inside the **pipe()** that says if **config.production**, let's
**minifyCSS**, else run this through a filter called **util.noop**.
>this **noop** filter does nothing "Returns a stream that does nothing but pass data straight through."

```
gulp.task('sass', function() {
   gulp.src(config.assetsDir+'/'+config.sassPattern)
       .pipe(sourcemaps.init())
       .pipe(sass())
       .pipe(concat('main.css'))
       .pipe(config.production ? cleanCSS() : util.noop())
       .pipe(sourcemaps.write('.'))
       .pipe(gulp.dest('web/css'));
});
```
Now if I run just **gulp** the **main.css** is not minified.

But if I run the **gulp --production** There are no more whitespaces so the minify is working
in this case.


Links:
-----
* [gulp-conat][43]
* [gulp-clean-css][44]
* [gulp-util][45]



April 22, 2016 (GULP: sass to css, gulp-sourcemaps)
===================================================

I made a project specific Sass file that lives in **app/Resources/assets**. This is where my
frontend assets, but it doesn't matter. But this is not a public directory. Gulp firs job
will be to turn that Sass file into CSS and to put the finished file in the **web/css**
directory where it can be accessed, where its public.

##### Installing gulp-sass
With Gulp, we can make tasks, but it doesn't do much else. Most things are done with a plugin.
In Gulp's site there are more then 2345 [Plugins][39].

First install the [sass plugin][40]:
```
npm install gulp-sass --save-dev
```
I added the **--save-dev** because I want this plugin to be added to the **package.json** file.
That means that if I want to clone this project i need to run just **npm install** and it'll
download this stuff automatically.

##### The pipe Workflow

In the sass docs, its shows the classic Gulp workflow. We start by saying **gulp.src()** to
load files matching a pattern. Next pipe it through a filter - in this case **sass()**. and
pipe it once more with **gulp.dest()** - this will write the finished files.

So to do it copy the **required** line from the docs and add itt to the top of my file.
Now **gulp.src** let's load all the Sass files that are in that **sass/** directory so
__app/Resources/assets/sass/**/*.scss__:

```
gulpfile.js

var gulp = require('gulp');
var sass = require('gulp-sass');

gulp.task('default', function() {
   gulp.src('app/Resources/assets/sass/**/*.scss')
       .pipe(sass())
       .pipe(gulp.dest('web/css'));
});
```

The double __**__ tells Gulp to look recursively inside the **sass** directory for **.scc**
files. That will let me create subdirectories later.

Now that we loaded the files, we'll just pipe through whatever we need. Use **pipe()** then
**sass()** inside of it. Gulp works with streams, so imagine Gulp is opening up all of the
**.scss** files as a big stream and then passing them one-by-one through the **pipe()** function.
At this point all that Sass has been processed. Then finally, we'll pipe that to **gulp.dest()**
Basically saying dump the finished product to the **web/css** directory.

That's all we need, goo back to the terminal and just type **gulp**.

To use the CSS file that generated **styles.css** add the **link** tag to the base template.
By using the **asset()** function from Symfony, that's not actually doing anything here.
The path is relative to the public directory **web/** for a Symfony project.

Finally ignore the directory in git that Gulp is generating, because its going to change
every time I use Gulp:
```
/web/css
````

### gulp sourcemaps

When I inspect the elements on my page, the browser is looking at the final, processed file.
And that means that debugging CSS is going to be a nightmare.

Using gulp-sourcemaps plugin I can change this behavior. In the plugins page search for
[gulp-sourcemaps][41].

First is always the same install with __npm__:

```
npm install gulp-sourcemaps --save-dev
```
Next copy the **require** statement and put that on top:

```
gulpfile.js

var gulp = require('gulp');
var sass = require('gulp-sass');
var sourcemaps = require('gulp-sourcemaps');
...
```
Then activate it before piping through any filters that may change which line some code lives on.
So before the **sass()** line, use **pipe()** with **sourcemaps.init()** inside. Then after
the filters are done, pipe it again through **sourcemaps.write('.')**:

```
gulpfile.js
...

gulp.task('default', function() {
   gulp.src('app/Resources/assets/sass/**/*.scss')
       .pipe(sourcemaps.init())
       .pipe(sass())
       .pipe(sourcemaps.write('.'))
       .pipe(gulp.dest('web/css'));
});
```
Run **gulp** in the terminal. No errors. And now there are two generated files the **styles.css** and
the **styles.css.map**. That's what the **.** did - it told Gulp to put the map file right
in the same directory as **styles.css**, and the browser know to look there. Now if I
refresh the page again. Inspect the element shows now the styling from **styles.scss**.
Now we can do whatever processing we want and we don't have to worry about killing our
debugging.

**gulp-sourcemaps** and **gulp-sass** work together because [sourcemaps supports][42] more gulp plugins.

Links:
-----
* [Gulp Plugins][39]
* [sass plugin][40]
* [gulp-sourcemaps plugin][41]
* [sourcemaps wiki for supported gulp plugins][42]



April 21, 2016 (gulp)
=====================

#### Installing gulp.

The Node Package Manager - or npm is the composer of the Node.js world.
```
npm install -g gulp
```

This command will give a **gulp** executable. To test if it's works:

```
gulp -v
```
**-v** will show us the gul version installed.

We know that Composer works by reading a **composer.json** file and downloading everything
into a **vendor/** directory. **npm** does the same thing. It reads from a **package.json**
and downloads everything into a **node_modules** directory. To get the **package.json** type:

```
npm init
```
Hit enter and go through the questions. After that open the **package.json**:

```
{
  "name": "gulp-recup",
  "version": "1.0.0",
  "description": "The project RecUp\r =================",
  "main": "index.js",
  "scripts": {
    "test": "echo \"Error: no test specified\" && exit 1"
  },
  "repository": {
    "type": "git",
    "url": "git+https://github.com/djanov/recup.git"
  },
  "author": "Daniel",
  "license": "ISC",
  "bugs": {
    "url": "https://github.com/djanov/recup/issues"
  }
}
```
#### Installing Gulp into the project

Now we can install NOde packages into the project. Install **gulp**:

```
npm install gulp --save-dev
```
The original command with the **-g** gave us the global **gulp** executable. This time we're
actually installing gulp into our project so other libraries can use it. The **---save-dev**
part says "download this into my project AND add an entry into **package.json** for it."
Now open **package.json** again now it has a new **devDependencies** section and have the new
**node_modules** directory with **gulp** in it.

```
{
  "name": "gulp-recup",
  "version": "1.0.0",
   ...
  },
  "homepage": "https://github.com/djanov/recup#readme",
  "devDependencies": {
    "gulp": "^3.9.1"
  }
}
```
In Composer terms, **devDependencies** is the **require** key in **composer.json** and
**node_modules** is the **vendor/** directory.

To test if the gulp is working create a new file **gulpfile.js** at the root of the project.
Gulp looks for this. Now create a **default** task and use the **console.log** to test this out.

```
var gulp = require('gulp');

gulp.task('default', function() {
   console.log('gulp test');
});
```

Now go back to the command line and type **gulp** followed by the name of the task **default**:

```
gulp default
```
But if we type just **gulp** we will get the sam thing. The task called **default** is the
"default" task and runs if we don't include the name.

Links:
-----
* [Using Gulp to Manage Components][36]
* [Setting up Symfony2 with Gulp and Bower][37]
* [KNP First Gulp][38]



April 19, 2016 (new branch, bower)
==================================

#### Making a new branch

To view all existing branches type:

```
git branch -a
```
Adding the "-a" to the end of the command, this tells GIT that we want to see all branches that exist,
including ones that we do not have in our local workspace.

To create a new branch, named knp, type the following:
```
git checkout -b knp
```
This will automatically switch to the new branch. To switch back and forth between the two branches, use
the git checkout command:

```
git checkout master
```
or
```
git checkout knp
```

If we are making changes or adding a new file to the new branch, until we merge it to the master branch,
it will not exist there. Now i make the changes in the README.md file and going to commit to master branch
so the knp branch going to be behind.

#### Merging code between branches

The process of moving code between branches(often from development to production) is know as __merging__.

It is important to remember when merging, that we want to be on tha branch that we want to merge to.
One of the options that we can pass to the merge command, "--no-ff", means we want to git to retain all of the
commit messages prior to the merge. This will make tracking changes easier in the future.

To merge the changes from the knp branch to the master branch, we are now in the master branch, type the
following:

```
git merge develop --no-ff
```

The last thing to do, to make this change in the remote server is to push the changes

```
git push origin master
git push origin knp
```

### Bower

Installing bower to the project for managing front-end dependencies (jquery, bootstrap, font-awesome).
Bower is built on top of Node.js. Make sure to install bower. To install power globally:
```
npm install -g bower
```

##### Configuring Bower in the Project

Normally, Bower downloads everything into **bower_components/** directory. In Symfony, only files in
**web/** directory are publicly accessible, so we need to configure Bower to download things there, but
I going to use **Gulp**, so i will set the directory to **"vendor/bower_components"**. And with Gulp i will in the end
move all assets into the **web/** directory.

So make the new file in the root directory **.bowercc**
```
{
  "directory": "vendor/bower_components"
}
```

##### Installing Bootstrap, jQuery, Font-awesome

To create a **bower.json** file, just run **bower init**. And now we're ready to start adding things to the project:

```
bower install --save jquery bootstrap fontawesome
```

This will install Bootstrap, jQuery, Font-awesome and its dependencies in "vendor/bower_components"

>**Tip:** If we see a bower.json file we can just run "bower install" and bower will install the right
  versions of the packages we need and their dependencies.

Links:
-----
* [How To Use Git Branches][32]
* [Using Bower with Symfony][33]
* [Bower][34]
* [Managing Fronted Dependencies][35]



April 18, 2016 (Injecting the Cache Service, adding the twig extension)
=======================================================================

Add caching to **MarkdownTransformer**.

Copy part of the old caching code and paste that into the **parse()** function, and assign the method call
to the **str** variable and use the **$cache->save()** from the old code, and return **$str** and re add the
**sleep()** call to see the difference in the caching in prod in dev environment:

```
src/RecUp/RecordBundle/Service/MarkdownTransformer.php

class MarkdownTransformer
{
   ...
    public function parse($str)
    {
        $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
        $key = md5($str);
        if ($cache->contains($key)) {
            return $cache->fetch($key);
        }

        sleep(1);
        $str = $this->markdownParser
            ->transformMarkdown($str);
        $cache->save($key, $str);

        return $str;
    }
}
```

This won't work there is no **get()** function in this class. And we don't have access to the
**doctrine_cache.provider.my_markdown_cache** service. To get access use Dependency injection.

**Dependency injection**

Add a second argument to the constructor called **$cache**. And give a type-hint. Copy the service name
and run:
```
php app/console debug:container doctrine_cache.providers.my_markdown_cache
```
The service is an instance of **ArrayCache**. Don't type-hint that, we have set in the config.yml that
uses **ArrayCache** in the **dev** environment and **FilesystemCache** in **prod**:
```
...
parameters:
    locale: en
    cache_type: file_system
...
doctrine_cache:
    providers:
        my_markdown_cache:
            type: %cache_type%
            file_system:
                directory: %kernel.cache_dir%/markdown_cache

```
If we type-hint with **ArrayCache**, this will explode in **prod** because this service will be a different
class. So do some digging **ArrayCache** extends **CacheProvider** implements several interface and one of
them is just called **Cache**. And that's what we need it contain the methods we're using but if there is
any problem PhpStorm will highlighting those after we add the type-hint, i mean if there is a missing method
for example.Then use a shortcut **alt + enter** in windows and select initialize fields, this will add the **private $cache**
property and set it in **__construct()**.

```
<?php
src/RecUp/RecordBundle/Service/MarkdownTransformer.php

namespace AppBundle\Service;

use Doctrine\Common\Cache\Cache;
use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{
    ...
     private $cache;

    public function __construct(MarkdownParserInterface $markdownParser, Cache $cache)
    {
    ...
     $this->cache = $cache;
    }
    ...
}
```

 Update **parse()** with **$cache = $this->cache**:

```
src/RecUp/RecordBundle/Service/MarkdownTransformer.php

class MarkdownTransformer
{
    ...
    public function parse($str)
    {
        $cache = $this->cache;
    ...
    }
}
```
Next because we added a new constructor argument, we need to update any code that instantiates the
**MarkdownTransformer**. But now, that's not done by us: it's done by Symfony, and we help it in
**services.yml**. Under arguments, add a comma and quotes. Copy the service name - **@doctrine_cache.providers.my_markdown_cache**
and paste it here:

```
app/config/services.yml

services:
     app.markdown_transformer:
         class: RecUp\RecordBundle\Service\MarkdownTransformer
         arguments: ['@markdown.parser', '@doctrine_cache.providers.my_markdown_cache']
```

Clear the **prod** cache:

```
php app/console cache:clear --evn=prod
```
Now if we test in prod environment the first time is going to be slow, but hen faster after. Caching is working.
We can see the cache file in that was generated by the configuration in the **config.yml** for the caching in
**app/cache/prod/markdown_cache**. In **dev** environment if we refresh the page it will be slow every time,
because the caching is configured to work only in the **prod** environment.

### Adding the Twig Extension

Because the **about** needs to be parsed through markdown, we have to pass it as an independent variable into
the template.The **KnpMarkdownBundle** comes with a filter called **markdown** so we can use that

```
src/RecUp/RecordBundle/Resources/views/Default/show.html.twig
   ...
   <dt>About:</dt>
   <dd>{{ name.about|markdownify }}</dd>
   ...
```

If we refresh we see this parses the string to markdown, if we view the HTML there's a **p** tag for the
process.

The **markdown** filter uses the **markdown.parser** service from **KnpMarkdownBundle** - it does not use
my **app.markdown_transformer**. And this means that it's not using the caching system too.I need to create
a Twig filter

**Creating a Twig Extension**

To do that, create a directory called **Twig** and the name is not important, inside create new php class
**MarkdownExtension**, and remember Twig is its own, independent library.In the Twig's documentation to create
a Twig extension, it tell to create a class, make it extend **\Twig_Extension** and then fill in some methods.
Using the code generate menu **alt + insert** on windows implements methods, the one method I must have is
called **getName()**. Just make it return any unique string like **app_markdown**:

```
src/RecUp/RecordBundle/Twig/MarkdownExtension.php

use RecUp\RecordBundle\Service\MarkdownTransformer;

class MarkdownExtension extends \Twig_Extension
{
   nction getName()
    {
        return 'app_markdown';
    }
 }
```

To add a new filter, use the **getFilters()** method from the code generating menu "Override Methods", and
here return an array of new filters: each is described by a new **\Twig_SimpleFilter** object. The first
argument will be the filter name - **markdownify**, then point to a function in this class that should be
called when the filter is used **parseMarkdown** and the third argument an options array, and add **is_safe**
 set to an array containing **html**. This means it's always safe to output contents of this filter in
 HTML:

```
src/RecUp/RecordBundle/Twig/MarkdownExtension.php

    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('markdownify', array($this, 'parseMarkdown'), [
                'is_safe' => ['html']
            ])
        ];
    }
```

We need to tell Twig about the Twig extension. To do that, first register it as a service. The name doesn't
matter, so use **app.markdown_extension**. Set the class but skip **arguments** we will use **autowire**.
Twig to know about this service we add a tag. The syntax is weird add **tags:**, then under that a dash and
set of curly-braces. Inside a set **name** to **twig.extension**, and add **autowire: true** under,
Symfony reads the type-hints for each constructor argument, and tries to automatically find the correct service
to pass. In this case the **MarkdownTransformer** type-hint and knew to use the **app.markdown_transformer**
service: since that is an instance of this class, the construct will be added after this. This doesn't always work
 but Symfony will give a clear exception if it can't figure out what to do.

 ```
services:
    ...
    app.markdown_extension:
         class: RecUp\RecordBundle\Twig\MarkdownExtension
         tags:
            - { name: twig.extension }
         autowire: true
 ```

 Now add the type-hint for autowire to work.We need to parse this through markdown. So we need to access
 other service: **MarkdownTransformer** use Dependency Injection!

 ```
 src/RecUp/RecordBundle/Twig/MarkdownExtension.php

 use RecUp\RecordBundle\Service\MarkdownTransformer;

 class MarkdownExtension extends \Twig_Extension
 {
    private $markdownTransformer;

     public function __construct(MarkdownTransformer $markdownTransformer)
     {

         $this->markdownTransformer = $markdownTransformer;
     }
     ...
  }
```
Now that we have that the autowire will work in the services, because its reads type-hint for each
constructor argument.

In **parseMarkdown()**, return **$this->markdownTransformer->parse()** and pass it **$str**

```
 src/RecUp/RecordBundle/Twig/MarkdownExtension.php

 class MarkdownExtension extends \Twig_Extension
 {
 ...
  public function parseMarkdown($str)
    {
        return $this->markdownTransformer->parse($str);
    }
 }
```

Explaining Twig custom extension how it works.
First if we need an twig extension it must extends **Twig_Extension**
and need to have **getName()** method to return a unique identifier for the extension name.
After that I add the **__construct** function because i need dependency injection in other word i need my own service to
use my own parse function.
Then that using the **getFilter()** method the first argument is the **markdownify**
is the name of the filter,
the second argument is the name of the function that will call on this **parseMarkdown**
and the third argument is an options array **'is_safe' => ['html']** means that it's always safe to output
contents of this filter in HTML.
The last method **parseMarkdown($str)** is being called when the filter is used **markdownify** and its using
my service **markdownTransformer** and the **parse($str)** method is called that will cache and parse through markdown
(Converts text to html using markdown rules - by the **transformMarkdown**).

Links:
-----
* [The Dependency Injection Tags][29]
* [Creating an Extension (Twig)][30]
* [Twig Extension][31]



April 17, 2016 (Type-Hint, register service in the container)
=============================================================

When we need to add a type-hint to make our code clearer, and avoid errors in case we accidentally pass in
something else. To found out what type of object is **$markdownParser** argument first run:

```
php app/console debug:container markdown
```

And select **markdown.parser** that's the service we're passing into **MarkdownTransformer. It's an
instance of **Knp\Bundle\MarkdownBundle\Parser\Preset\Max**. We can use that as type-hint. But don't do that
press **shift** + **shift**, type "max" and open that class:

```
 vendor/knplabs/knp-markdown-bundle/Parser/Preset/Max.php

/**
 * Full featured Markdown Parser
 */
class Max extends MarkdownParser
{

}
```

This extends **MarkdownParser** and that does all the work:

```
 vendor/knplabs/knp-markdown-bundle/Parser/MarkdownParser.php

/**
 * MarkdownParser
 *
 * This class extends the original Markdown parser.
 * It allows to disable unwanted features to increase performances.
 */
class MarkdownParser extends MarkdownExtra implements MarkdownParserInterface
{

}

```

And MarkdownParser implements a **MarkdownParserInterface**. So now we have 3 options to type-hint with:
**Max**, **MarkdownParser** or **MarkdownParserInterface**. They will all work, but when possible, it's
best to find a base class or better the interface that has the methods on it you need, and use that,
Type-hint the argument with **MarkdownParserInterface**:

```
src/RecUp/RecordBundle/Service/MarkdownTransformer.php

use Knp\Bundle\MarkdownBundle\MarkdownParserInterface;

class MarkdownTransformer
{

    public function __construct(MarkdownParserInterface $markdownParser)

}
```

This is the best option for two reasons. First, in theory we could swap out the **$markdownParser** for
a different object, as long as it implements this interface. Second, it's really clear what methods can
call on the **$markdownParser** property: only those on that interface.
But now PhpStorm is angry about calling **transform()** on **$this->markdownParser**:

>Method "transform" not found in class **MarkdownParserInterface**

Open that interface, it has only one method: **transformMarkdown()**

```
vendor/knplabs/knp-markdown-bundle/MarkdownParserInterface.php

interface MarkdownParserInterface
{
    /**
     * Converts text to html using markdown rules
     *
     * @param string $text plain text
     *
     * @return string rendered html
     */
    function transformMarkdown($text);
}
```
Everything will work right now, refresh to see.
The weirdness is just that we are forcing an object that implements **MarkdownParserInterface** to be
passed in, but then we're calling a method that's not on that interface. Change our call to
**transformMarkdown**:

```
src/RecUp/RecordBundle/Service/MarkdownTransformer.php

class MarkdownTransformer
{

    public function parse($str)
    {
        return $this->markdownParser
            ->transformMarkdown($str);
    }
}
```

Inside **MarkdownParser**, we can see that **transformMarkdown()** and **transform()** do the same thing:

```
vendor/knplabs/knp-markdown-bundle/Parser/MarkdownParser.php

class MarkdownParser extends MarkdownExtra implements MarkdownParserInterface
{

    public function transformMarkdown($text)
    {
        return parent::transform($text);
    }

}
```
This did not change any behavior: it just made code more portable: the class will work with any object
that implements **MarkdownParserInterface**.

So when we need an object from inside a class, use dependency  injection. And then add the **__construct()**
argument, type-hint it with either the class see in **debug:container** or an interface if you can find one.
Both will work.

### Register the Service in the Container

The **MarkdownTransformer** class does not live in the container like **markdown.parser**, **logger** or
anything else we see in **debug:container**. We need to instantiate it manually: we can't just say like
**$this->get('app.markdown_transformer')** and expect the container to create it for us.

Open up **app/config/services.yml** and add a new service to the container. Under the **services** key
give the new service a nickname **app.markdown_transformer**. This can be anything we'll use tit later to
fetch the service. Next in order to the container to instantiate this, it needs to know two things: the
class name and what arguments to pass to the constructor. Add first the **class** then the full
**RecUp\RecordBundle\Service\MarkdownTransformer**, for the second add **arguments:** then make a YML
array: **[]**. These ara the constrictor arguments, it's pretty simple. If we used for example
**[sunshine, rainbows]**, it would pass the string **sunshine** as the first argument to
**MarkdownTransformer** and **rainbow** as the second. And that would be it. In reality, **MarkdownTransformer**
requires one argument: the **markdown.parser** service. To tell the container to pass that add **@markdown.parser**:

```
app/config/services.yml

...
services:
     app.markdown_transformer:
         class: RecUp\RecordBundle\Service\MarkdownTransformer
         arguments: ['@markdown.parser']
```
The **@** is a special: it says, don't pass the string **markdown.parser**, pass the service **markdown.parser**.

And with 4 lines of code, a new service has been born in the container. To see if it's working:

```
php app/console debug:container markdown
```

To use it, instead of **new MarkdownTransformer()** use it like **$transformer = $this->get('app.markdown_transformer)**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

class DefaultController extends Controller
{
    ...
    public function showAction($track)
    {
    ...
        $markdownTransformer = $this->get('app.markdown_transformer');
    ...
    }
    ...
}
```
When this line runs, the container will create the **MarkdownTransformer** object behind the scenes.

**Why add a Service to the Container**

When we add a service to the container, we get two great thing. First using the service is much easier:
**$this->get('app.markdown_transformer)**. We don't need to worry about passing the constructor arguments,
we have that set-up in the services.yml, hence it could have ten constructor arguments and this simple line
would stay the same.

Second. if we ask for the **app.markdown_transformer** service more then once during a request, the container
only creates one of them: it returns that same one object each time. That's good for the performance. And
the container doesn't create the **MarkdownTransformer** object until and unless somebody asks for it. That
means that adding more services to the container does not slow things down.

**The Dumped Container**

Open the **cache/dev/appDevDebugProjectContainer.php**, this is the **container**: it's a class that's
dynamically built from the configuration. Find the "MarkdownTransformer" and find the **getApp_MarkdownTransformerService()**
method:

```
cache/dev/appDevDebugProjectContainer.php
class appDevDebugProjectContainer extends Container
{

    public function __construct()
    {

        $this->methodMap = array(

            'app.markdown_transformer' => 'getApp_MarkdownTransformerService',

        );

    }

    /**
     * Gets the 'app.markdown_transformer' service.
     *
     * This service is shared.
     * This method always returns the same instance of the service.
     *
     * @return \AppBundle\Service\MarkdownTransformer A AppBundle\Service\MarkdownTransformer instance.
     */
    protected function getApp_MarkdownTransformerService()
    {
        return $this->services['app.markdown_transformer'] = new \AppBundle\Service\MarkdownTransformer($this->get('markdown.parser'));
    }

}
```
When we ask for the **app.markdown_transformer** service, this method is called. It runs plain PHP ode that
we had before in the controller.

The configuration wrote in **services.yml** causes Symfony to write plain PHP code that creates my service
objects. We describe how to instantiate the object, and Symfony writes the PHP code to do that, and this
makes the container very fast.



April 16, 2016 (Dependency Injection)
=====================================

The **MarkdownTransformer** will do two thing: parse markdown and eventually cache it.
Let's start with the first.

If we are not in the controller - we don't have access to the container, in services or anything
so we can't get access to the **MarkdownTransformer**. To get access to the markdown parser object
inside **MarkdownTransformer**, we need [Dependency Injection][28].

Whenever we're inside of a class and we need access to an object that we don't have - like
markdown parser - add **public function __construct()** and add the object we need as an
argument:

```
src/RecUp/RecordBundle/Services/MarkdownTransformer.php

class MarkdownTransformer
{

    public function __construct($markdownParser)
    {

    }

}
```
Next create a private property and in the constructor, assign that to the object and use the
property, use it in **parse()**. So get rid of the **$this->get()** and just use **$this->markdownParser**:

```
src/RecUp/RecordBundle/Services/MarkdownTransformer.php

class MarkdownTransformer
{
    private $markdownParser;

    public function __construct($markdownParser)
    {
        $this->markdownParser = $markdownParser;
    }

    public function parse($str)
    {
       return $this->markdownParser
       ->transform($str);
    }
}
```

To finish this in the **DefaultController**, where we do have access to the object, pass in **$this->get('markdown.parser')**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

class DefaultController extends Controller
{
    ...
    public function showAction($track)
    {
        ...
        $markdownTransformer = new MarkdownTransformer(
            $this->get('markdown.parser')
        );
        ...
    }
    ...
}
```

I's alive Twig is escaping the **<p>** tag so that proves hat markdown parsing is happening. This process is
dependency injection. It basically says: if an object needs something, we should pass it to the object.

Links:
------
* [Dependency Injection][28]



April 15, 2016 (services, creating a Service class)
===================================================

Symfony is just a big container of useful objects called **services**, and everything
that happens is actually done by one of these. For example the **render()** function
is live in Symfony's base **Controller** it doesn't do any work, just finds the **templating**
service that renders templates. To get a big list of services run:

```
php app/console debug:container
```

We can also control these services in **app/config/config.yml**, for example the twig configuration:

```
app/config/config.yml

# Twig Configuration
twig:
    debug:            "%kernel.debug%"
    strict_variables: "%kernel.debug%"

    number_format:
      thousands_separator: ','
```

**New goal make service Architecture**

In the **DefaultController** at the **showAction()** we have 15 lines of code that parsed **$funFact**
 through Markdown and then cached it. We need to have these 15 lines of code **outside of our
 controller** because of three reasons:

 1.) Can't re-use this. If we need to do parse some markdown somewhere else, we can copy and pate,
 but that's a horrible thing to do.

 2.) It's not instantly clear what these 15 lines do, need to take time and read them to find out.

 3.) If we want to unit test this code, we can't. To unit test something, it needs to live in its
 own, isolated, focused class.

### Creating a Service Class

Move the chunk of a code out of the controller!

First, create a new PHP class, In **RecordBundle**, create a new directory called **Service**
but that could be called anything. Inside, add a new PHP class called **MarkdownTransformer**,
that could also be called anything. First try the service if its working with a simple example:
Make a public function **parse()** with a **$str** argument and return a php function **strtoupper**:

```
src/RecUp/RecordBundle/Service/MarkdownTransformer.php

class MarkdownTransformer
{
    public function parse($str)
    {
        return strtoupper($str);
    }
}
```

Next in **DefaultController**, create the new object with **$markdownParser = new MarkdownTransformer()**
PphpStorm will add the use statement **use RecUp\RecordBundle\Service\MarkdownTransformer;**.
Next add **$about = $markdownParser->parse()** and pass **$songs->getAbout()**.
Finish this by passing the **$about** into the template so we can render the parsed version:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

class DefaultController extends Controller
{
      ...
      public function showAction($track)
      {
         $markdownParser = new MarkdownTransformer();
         $about = $markdownParser->parse($songs->getAbout());

      ...
         return $this->render('@Record/Default/show.html.twig', array(
              'name' => $songs,
              'recentCommentCount' => count($recentComments),
              'about' => $about,
          ));
      }
      ...
}
```
Finally open the template and add the **about**
```
src/RecUp/RecordBundle/Resources/views/Default/show.html.twig

{% block body %}
            ...
               <dt>About:</dt>
               <dd>{{ about }}</dd>
            ...
{% endblock %}
```

Open ```localhost:8000/songs``` click one of them, and there is the about section
 in upper case.

 The most important and commonly-confusing object-oriented strategies that exist anywhere
 in any language. And it's this: you should take chunks of code that do things and move them
 into an outside function in an outside class. That's it.

 And **MarkdownTransform** is a service, because remember, a service is just a class that does
 work for us. And when you isolate a lot of your code into these service classes, you start
 build what's called a "service-oriented architecture". That basically means that instead
 of having all of your code in big controllers, you organize them into nice little services
 that each do one job.



April 14, 2016
==============
tricks with ArrayCollection for showing recent comments (bad way if we have many comments), Querying on a relationship, query using JOIN
----------------------------------------------------------------------------------------------------------------------------------------

To make a new section on top to easily see how many comments have been posted during the past 3 months, in **showAction()**, we need to count all the recent notes from **Record**.
The **getComments()** returns an **ArrayCollection** object and it has some tricks on it, one of that is **filter()** method. Make things work call the **getComments()** and call on that **filter()** method and pass an anonymous function with a **RecordComment** argument. The **ArrayCollection** will call this function for each item. If we return true, it says. If we return false, it disappears.
Next pass the new **recentCommentCount** variable into twig that's set to **count($recentComments)**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

class DefaultController extends Controller
{
    ...
     public function showAction($track) {
     ...
      $recentComments = $songs->getComments()
             ->filter(function(RecordComment $comment){
                 return $comment->getCreatedAt() > new \DateTime('-3 months');
     });
         return $this->render('@Record/Default/show.html.twig', array(
             'name' => $songs,
             'recentCommentCount' => count($recentComments)
         ));
     }
}
```

In the template, add a new **dt** for **Recent Comments** and a **dd** with **{{recentCommentCount}}**:

```
src/RecUp/RecordBundle/Resources/views/Default/show.html.twig

{% block body %}
    ...
      <dt>Recent Comments</dt>
     <dd>{{ recentCommentCount }}</dd>
     ...
{% endblock %}
```

Now if we refresh we see for example six comments, but we have a lot more than six in total but remember we only want to know the recent comments (< 3 months).
The [ArrayCollection][27] has a lot of methods like this for example **contains()**, **containsKey()**, **forAll()**, **map()** and others.

**Notice:**
**Don't use ArrayCollection** if we have many comments, because **ArrayCollection** queries for all of the comments, even though we don't need them all, and we will fell the performance impact of loading up hundreds of extra objects.

So for this case make the custom query that only returns the **RecordComment** object we need.

### Querying on a Relationship

Create a query that returns the RecordComment that belong to a specific **Record** and are less then 3 months old. To keep things organize, custom queries to the **RecordComment** table should live in the **RecordCommentRepository**. So create it.
After creating it add a new **public function findAllRecentCommentsForRecord()** and give it a **Record** argument, next just like before when creating custom queries use the query builder:

```
src/RecUp/RecordBundle/Repository/RecordCommentRepository.php

class RecordCommentRepository extends EntityRepository
{
    /**
     * @param Record $record
     * @return RecordComment[]
     */
    public function findAllRecentCommentsForRecord(Record $record)
    {
    return $this->createQueryBuilder('record_comment')
            ->getQuery()
            ->execute();
    }
}
```

Doctrine does not know about this new repository class yet, so go and add itt to **RecordComment**, find the **@ORM\Entity** and add the repositoryclass:

```
src/RecUp/RecordBundle/Entity/RecordComment.php

/**
 * @ORM\Entity(repositoryClass="RecUp\RecordBundle\Repository\RecordCommentRepository")
 * @ORM\Table(name="record_comment")
 */
class RecordComment
{

}
```

Finally, use the new method in **DefaultController** and render the **$recentComments**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

class DefaultController extends Controller
{
     public function showAction($track)
     {
     ...
      $recentComments = $em->getRepository('RecordBundle:RecordComment')
             ->findAllRecentCommentsForRecord($songs);

         return $this->render('@Record/Default/show.html.twig', array(
             'name' => $songs,
             'recentCommentCount' => count($recentComments)
         ));
     }
}
```

If we refresh we see the 100 notes, so we need to customize the query using the relationships:

```
src/RecUp/RecordBundle/Repository/RecordCommentRepository.php

class RecordCommentRepository extends EntityRepository
{
    /**
     * @param Record $record
     * @return RecordComment[]
     */
    public function findAllRecentCommentsForRecord(Record $record)
    {
    return $this->createQueryBuilder('record_comment')
       return $this->createQueryBuilder('record_comment')
            ->andWhere('record_comment.record = :record')          // return only the comments belongs to record
            ->setParameter('record', $record)
            ->andWhere('record_comment.createdAt > :recentDate')  // return value only the recent 3 months
            ->setParameter('recentDate', new \DateTime('-3 months'))
            ->getQuery()
            ->execute();
    }
}
```

Now we have the same result just before using the ArrayCollection but we have now better performance and  more customizability.

### Query using JOIN

Order **songs** by the most recent comment - a column that lives on an entirely different table.

In **SongRepository** rename the query function name to **findAllPublishedOrderedByRecentlyActive**:

```
src/RecUp/RecordBundle/Repository/SongsRepository.php

class SongsRepository extends EntityRepository
{
    /**
     * @return Record[]
     */
    public function findAllPublishedOrderedByRecentlyActive()
    {

    }
}
```
Also change in **DefaultController** too:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

class DefaultController extends Controller
{
 ...
  public function listAction()
     {
         $em = $this->getDoctrine()->getManager();


         $songs = $em->getRepository('RecordBundle:Record')
             ->findAllPublishedOrderedByRecentlyActive();
         ...
     }
}
```
Now we need to order by the **createdAt** field in the **record_comment** table. And to do that in SQL we need to join over to that table. Do that with, **->leftJoin('song.comments')** because the alis was set to song in the QueryBuilder.
**comments** property name is the name in **Record** that references  the relationship, and just by mentioning it, Doctrine has all the info it needs to generate the full JOIN SQL.
Give the **leftJoin()** a second argument: **record_comment** - this is the alias we can use during the rest of the query to reference fields on the joined **record_comment** table. With this we can order the comments to be the newest first:

```
src/RecUp/RecordBundle/Repository/SongsRepository.php

class SongsRepository extends EntityRepository
{
    /**
     * @return Record[]
     */
    public function findAllPublishedOrderedByRecentlyActive()
    {
            return $this->createQueryBuilder('song')
                ->andWhere('song.isPublished = :isPublished')
                ->setParameter('isPublished', true)
                ->leftJoin('song.comments', 'record_comment')
                ->orderBy('record_comment.createdAt', 'DESC')
                ->getQuery()
                ->execute();
    }
}
```

Refresh and the order did change, and its kinda working (not working entirely need to check?!)

And this is working because we have set the inverse side of the relationship, we added this for the **$record->getComments**, and this is the second reason to do the inverse side of the relation, if we're doing a JOIN in this direction.
>**Tip:** it is possible to query over this join without mapping the side of the relationship, but that is more complicated.


Links:
------
* [ArrayCollection][27]



April 13, 2016 (final steps for dynamic comments)
=================================================

Remove the hardcoded stuff in the **getNoteAction** add make it dynamic.
First in the **Record** entity add annotation for the **getComments** so that PhpStorm have the auto-complete thi is not necessary.
 ```
 src/RecUp/RecordBundle/Entity/Record.php

 /**
 class Record
 {
  ...
      * @return ArrayCollection|RecordComment[]
      */
     public function getComments()
     {
         return $this->comments;
     }
}
 ```

Now create the **$comments** structure, with real data. Above the **$foreach** add a new **$comments** variable. Inside, add a new entry to that and start populating it with id, username, avatarUri, comment and createdAt:

```
src/RecUp/RecordBundle/Controller/DefaultController.php;

  public function getNoteAction(Record $record)
    {
        $comments = [];

        foreach($record->getComments() as $comment) {
            $comments[] = [
                'id' => $comment->getId(),
                'username' => $comment->getUsername(),
                'avatarUri' => '/images/'.$comment->getUserAvatarFilename(),
                'comment' => $comment->getComment(),
                'date' => $comment->getCreatedAt()->format('M, d, Y')
            ];
        }

        $data = [
            'notes' => $comments,
        ];

        return new JsonResponse($data);
    }
```

Refresh and there are the random comments (depends on how many are by the record(id) and the record_id in recordComment), using AJAX request, Alice and Faker. But the ordering is weird. To control that Open **Record** and add another annotation to **comment**:

```
src/RecUp/RecordBundle/Entity/Record.php

  /**
     * @ORM\OneToMany(targetEntity="RecordComment", mappedBy="record")
     * @ORM\OrderBy({"createdAt" = "DESC"})
    */
    private $comments;
```

Now the Newest ones on top, oldest ones on the bottom. So we have some control.

Links:
-----
* [More about Doctrine Annotations (example: OrderBy)][26]


April 11, 2016 (setting up the OneToMany inverse side of the relation)
======================================================================

**Setting up the OneToMany side**

We can think about any relationship in two directions: each **RecordComment** has one **Record**. Or each **Record** has many **RecordComment**. In Doctrine we can map just one side of a relationship, or both.
Open **Record** and add a new **comments** property. This is the inverse side of the relationship. Add a **OneToMAny** annotation with **targetEntity** set to **RecordComment** and a **mappedBy** set to **record** - that's the property in **RecordComment** that forms the main side of the relation:

```
src/RecUp/RecordBundle/Entity/Record.php

...
class Record {
    /**
     * @ORM\OneToMany(targetEntity="RecordComment", mappedBy="record")
     */
    private $comments;
    ...
}
```

Now there's still only one relation in the database: but now there are two ways to access the data on it: **$recordComment->getRecord()** and now **$record->getComments()**.

Add an **inversedBy** set to **comments** on this side: to pint to the other property:

```
src/RecUp/RecordBundle/Entity/RecordComment.php

class RecordComment 
{
     /**
     * @ORM\ManyToOne(targetEntity="Record", inversedBy="comments"))
     * @ORM\JoinColumn(nullable=false)
     */
    private $record;
}
```
Its in symfony documentation not sure why its needed.

This didn't cause any changes in the database: we just added some sugar to our Doctrine setup, we don't need to migrate.

**Add the ArrayCollection**

In **Record**, add a **__construct()** method and initialize the **comments** property to a new **ArrayCollection**:

```
src/RecUp/RecordBundle/Entity/Record.php

use Doctrine\Common\Collections\ArrayCollection;

class Record
{
 ...
   public function __construct()
    {
        $this->comments = new ArrayCollection();
    }
 ...
}
```
The object is like  a PHP array on steroids. We can loop over it like an array, but it has other super powers. Doctrine always returns one of these for relationships instead of a normal PHP array.

Finally, go to the bottom of the class and add a getter for **comments**:

```
src/RecUp/RecordBundle/Entity/Record.php

class Record
{
 ...
    public function getComments()
    {
        return $this->comments;
    }
}
```

To test it In **getNoteAction()** loop over **$record->getComments()** as **$comment** and **dump($comment)**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

class DefaultController extends Controller 
{
 ...
   public function getNoteAction(Record $record)
    {
        foreach($record->getComments() as $comment) {
            dump($comment);
        }
  ...
}
```
Refresh the app, let the AJAX call happen and the go to symfony debug toolbar and check the AJAX calls clcik the profile id and in debug side toolbar, to find the dump. A bunch of **RecordComment** objects. Check the Doctrine section: we can see the extra query that was made to fetch these. This query doesn't happen until we actually call **$record->getComments()**.

**Owning the Inverse sides**

Whenever we have a relation: start by figuring out which entity should have the foreign key column and then add the **ManToOne** relationship there first. This is the only side of the relationship that must have - it's called the "owning" side.

Mapping the other side - the **OneToMany** inverse side - is always optional. It's not needed until we need to - either because we want to cute shortcut like **$record->getComments()** or because we want to join the query from **Record** to **RecordComment**.

**ManyToMany** relationships - the only other real type of relationship - also have an owning and inverse side, but we can choose which is which.

**Notice:**

We didn't add a **setComment()** method to **Record**. That's because we cannot set data on the inverse side: we can only set it on the owning side. In other word, **$recordComment->setRecord()** will work, but **$record->setComments()** will not work: Doctrine will ignore that when saving.
So when we setup the inverse side of relation, do not generate the setter function.

Links:
------
* [Doctrine mappedBy & inversedBy][25]


April 10, 2016(first steps for making the comments dynamic)
=============================================================

The comments are loaded by ReactJS app. and that makes an AJAX call to an API endpoint in **DefaultController**. In **getNoteAction()**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

 /**
     * @Route("/test/{name}/notes", name="record_show_notes")
    * @Method("GET")
    */
    public function getNoteAction()
    {

    }
```
Step 1: use the **track** argument to query from **Record**. So get the entity manager, get the Record repository, and then call a method on it **findOneBy()**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

    /**
     * @Route("/test/{track}", name="record_show")
     */
    public function showAction($track)
{
    $em = $this->getDoctrine()->getManager();

    $songs = $em->getRepository('RecordBundle:Record')
        ->findOneBy(['songName' => $track]);
}
```
We have this already, now change the **{name}** in the route to **songName**. This doesn't change the URL to this page, but it does break all the links we have to this route.
To fix those, go to terminal and search for the route name:

```
git grep record_show_notes
```
We have used in one spot in **show.html.twig** open, and find at the bottom, just change the key from **name** to **songName**:

```
src/RecUp/RecordBundle/Resources/views/Default/show.htm.twig

{%  block javascripts %}
    ...
    <script type="text/babel">
        var notesUrl = '{{ path('record_show_notes', {'songName': name.songName}) }}'

      ...
    </script>
{% endblock %}
``` 

**Using parameters conversion**

Now type-hint the argument with the **Record** class and add **$record**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

    /**
     * @Route("/test/{songName}/notes", name="record_show_notes")
    * @Method("GET")
    */
    public function getNoteAction(Record $record)
    {
    ...
    }
```

I just violated one of the cardinal rules of routing: that every argument must match the name of a routing wildcard. If I type-hint an argument with an entity class name - like **Record** - Symfony will automatically query for it. This works as long as the wildcard has the same name as a property on **Record**. That's why i changed **name** to **songName**. And this is called "param conversion" more about [@ParamConverter][24].
Dump the **$record** to see it's working:

```
src/RecUp/RecordBundle/Controller/DefaultController.php
...
public function getNoteAction(Record $record)
    {
        dump($record);
    ...
    }
```
We won't see the dump because it's actually an AJAX call - one that happens automatically each second.

**Seeing the Profiler for an AJAX Request**

Go to the symfony debug toolbar and there are new AJAX request every second, click one of them and we can see the **Record** object, or click the profile id and in the **Debug** panel there's the dump.

**Notice:**

We can't always use param conversion. If we want to run custom query we can't use, we need then to use like before: get the entity manager and query like normal, Use the shortcut when it helps!

Links:
------
* [@ParamConverter][24]



April 8, 2016 (seting JoinColumn for record, fixing the realations in fixtures)
===============================================================================

Can I save a **RecordComment** without setting a **Record** on it? No! I need to make that false. Go to **ManyToOne** annotation and add a new annotation below it: **JoinColumn**. Inside set **nullable=false**:

```
src/RecUp/RecordBundle/Entity/RecordComment.php

    /**
     * @ORM\ManyToOne(targetEntity="Record"))
     * @ORM\JoinColumn(nullable=false)
     */
    private $record;
```
The **JoinColumn** annotation controls how the foreign key looks in the database. It's optional. Another option is **onDelete**: that changes the **ON DELETE** behavior in the database- the default is **RESTRICT** but we can also use **CASCADE** or **SET NULL**.

Before we make the migration we need to drop the database and then re-create it, and re-migrate from the beginning, because we have bunch of existing **RecordComment** rows in the database, and each still has a **null record_id**. I can't set that column to **NOT NULL** because of the data that's already in the database. (If the app were already deployed to production, we would need to fix the migration: maybe UPDATE each existing record_comment and set the record_id to the first record in the table.).

```
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:migrations:migrate
```

The last step is to fix the broken fixtures. We need to associate each **RecordComment** with a **Record**. In alice it's easy: use **record: @** then the internal name of one of the record - like **record_1**. But we can make to match this by random each time change to **record_**:

```
RecUp\RecordBundle\Entity\Record:
  record_{1..10}:
    ...

RecUp\RecordBundle\Entity\RecordComment:
  record.comment_{1..100}:
    username: <userName()>
    userAvatarFilename: '50%? leanna.jpg : ryan.jpg'
    comment: <paragraph()>
    createdAt: <dateTimeBetween('-6 months', 'now')>
    record: '@record_*'
```

The record_ is need to match how we called it before (record_{1..10}). but we can change that two (in this case) to anything (only can't for the actaul name of the object record) 

Reload the fixtures:
```
php app/console doctrine:fixtures:load
```
Check the result:
```
php app/console doctrine:query:sql "SELECT * FROM record_comment"
```
Every single one has a random record (id).



April 7, 2016 (Saving a Relationship)
======================================

Doctrine will create a **record_id** integer column for this property and a foreign key to **record**. Generate the getter and setter and add Record type to **setRecord**:

```
src/RecUp/RecordBundle/Entity/RecordComment

class RecordComment
{
    ...

    public function getRecord()
    {
        return $this->record;
    }

    public function setRecord(Record $record)
    {
        $this->record = $record;
    }
}
```
I will call the **setRecord()** and pass it an entire **Record** object not an ID.

**Generate the Migration**
```
php app/console doctrine:migrations:diff
```

And then check it out:

```
app/DoctrineMigrations/Version20160407204418

class Version20160407204418 extends AbstractMigration
{

    public function up(Schema $schema)
    {
      ...

        $this->addSql('ALTER TABLE record_comment ADD record_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE record_comment ADD CONSTRAINT FK_23AB52114DFD750C FOREIGN KEY (record_id) REFERENCES record (id)');
        $this->addSql('CREATE INDEX IDX_23AB52114DFD750C ON record_comment (record_id)');
    }
```
Even though we called the property **record**, it sets up the database exactly how would we have normally: with a **record_id** integer column and a foreign key.

Run the migration:

```
php app/console doctrine:migrations:migrate
```

**Saving a relation**

In the **DefaultController**. In **newAction**, create a new **RecordComment**:

```
src/Recup/RecordBundle/Controller/DefaultController.php

  $comment = new RecordComment();
  $comment->setUsername('Daniel');
  $comment->setUserAvatarFilename('ryan.jpeg');
  $comment->setComment('I think ths song is amazing');
  $comment->setCreatedAt(new \DateTime('-1 month'));
```

To link the **RecordComment** to **Record** is simple: **$note->setComment()** and pass it the entire **$record**. The only tricky part is that we set the entire object, not the ID. With Doctrine relations, we almost need to forget about ID's entirely: our job is to link one object to another. When we save, Doctrine works out the details of how this should look in the database.
Don't forget to persist the **$comment**:

```
src/Recup/RecordBundle/Controller/DefaultController.php

...
$comment->setRecord($record);

$em = $this->getDoctrine()->getManager();
$em->persist($record);
$em->persist($comment);
$em->flush();
```

And we can persist in any order. Doctrine automatically knows that it needs to insert the **$record** first and then the **$comment**.

Go to ```http://localhost:8000/record/new``` and, if we check the **phpmyadmin** we can see the new **record** and the new **comment** for the **record**, we can see that the **id** of the **record** is matched with the **comment record_id**.



April 6, 2016 (ManyToOne relation)
==================================
Each record will have many comments, but each comment that someone adds will relate to only one record. The two most common relations are  **ManyToOne** and **ManyToMany**. For example **ManyToMany** will be if each **product** had many tags, but also each tag related to many products.

To decide if we have a **ManyToOne** or **ManyToMany** relationship. Just answer this question:

> Do either of the sides of the relationship belong to only one of the other?

Each **RecordComment** belongs to only one **Record**, so we have a classic **ManyToOne** relationship.

Setting up a **ManyToOne**

A ManyToOne relation to work just go to **RecordComment** entity add ad a new private **$record** property  and give it a **ManyToOne** annotation. Inside that, add **targetEntity="Record"**:

```
src/RecUp/RecordBundle/Entity/RecordComment.php

/**
 * @ORM\ManyToOne(targetEntity="Record"))
 */
private $record;
```

If the two entities do not live in the same namespace/directory, then the targetEntity must use the full namespace **RecUp\RecordBundle\Entity\Record**. But in this situation we have the entities in the same namespace/directory.


April 5, 2016 (Creating new Entity (RecordComment) and making 100 dummy comments)
=================================================================================

**Create the RecordComment**

Create the new RcordComment.php entity in **src/RecUp/RecordBundle/Entity**. Copy the ORM **use** statement from **Record** that all entities need and paste it.
Next add the **ORM Class**.
Next add the properties **id**, **username**, **userAvatarFilename**, **comment** and **createdAt**.
Next make the setters and getters for all, except the $id, for id add only the getter.

```
<?php
src/RecUp/RecordBundle/Entity/RecordComment.php


namespace RecUp\RecordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="record_comment")
 */
class RecordComment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     */
    private $userAvatarFilename;

    /**
     * @ORM\Column(type="text")
     */
    private $comment;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUserAvatarFilename()
    {
        return $this->userAvatarFilename;
    }

    /**
     * @param mixed $userAvatarFilename
     */
    public function setUserAvatarFilename($userAvatarFilename)
    {
        $this->userAvatarFilename = $userAvatarFilename;
    }

    /**
     * @return mixed
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param mixed $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
}
```

**Generate Migrations**

Entity done. Now generate the migration and if everything is alright run the migration:

```
php app/console doctrine:migrations:diff
php app/console doctrine:migrations:migrate
```

**Adding Fixtures**

Open the **fixtures.yml** file and add a new section for **RecUp\RecordBundle\Entity\RecordComment**. and start like before: **record.comment_** and create 100 comments:

```
src/RecUp/RecordBundle/DataFixtures/ORM/fixtures.yml

RecUp\RecordBundle\Entity\RecordComment:
  record.comment_{1..100}:
    username: <userName()>
    userAvatarFilename: '50%? leanna.jpg : ryan.jpg'
    comment: <paragraph()>
    createdAt: <dateTimeBetween('-6 months', 'now')>
```
And fill each property using the Faker functions. for the **userAvatarFilename** let's select one of the 2 random jpg picture with Alice: **50%? leanna.jpeg : ryan.jpeg**.

Finally run the fixtures:

```
php app/console doctrine:fixtures:load
```

and check the query:

```
php app/console doctrine:query:sql 'SELECT * FROM record_comment'
```
And now we can see all 100 random generated comments.


April 4, 2016 (Making Custom Queries)
=====================================

To make the **isPublished** field to work, I need only yo show published songs on the list page. We have the **findAll()** and the **findOneBy** methods for the queries. We need to make our custom query.

**What is the Repository**

To query, we always use repository object, to see what object is dump it

```
src/RecUp/RecordBundle/Controller/DefaultController.php

public function listAction()
{
    $em = $this->getDoctrine()->getManager();

    dump($em->getRepository('RecordBundle:Record'));die;
}
```

Run the page, it turns out this is an **EntityRepository** object. From the core Doctrine. And this class has the methods like **findAll()** and **findOneBy()**.

**Creating own Repository**

To make new methods, we need to create our own repository class. Create new directory called **Repository** inside add a new class **SongRepository.php**. This is not important. Make sure to extend **EntityRepository** class to that, to have still the original helpful methods:

```
src/RecUp/RecordBundle/Repository/SongsRepository.php

<?php

namespace RecUp\RecordBundle\Repository;

use Doctrine\ORM\EntityRepository;


class SongsRepository extends EntityRepository
{

}
```

Next, we need to tell Doctrine to use this class instead when we call **getRepository()**. To do that open **Record**, and at the top, add **repositoryClass=** in the parentheses of the **@ORM\Entity**, then the full class name to the new **SongsRepository**.

```
src/RecUp/RecordBundle/Entity/Record.php

/**
 * @ORM\Entity(repositoryClass="RecUp\RecordBundle\Repository\SongsRepository")
 * @ORM\Table(name="record")
 */
class Record
```

>Now if we refresh we see the dump shows a **SongsRepository** object. Now we add the custom function to make custom queries. So, each entity that needs a custom query will have its own repository class. And every custom query you write will live inside of these repository classes. That's going to keep queries organized.

 **Adding a custom query**

 Add a new **public function**. The Doctrine naming convention is **findeALLSOMETHING** for array or **findSOMETHING** for a single result. so call the function **findAllPublished**. Custom queries always look the same: start with, return **$this->createQueryBuilder('song')**. This returns a **QueryBuilder**. Because we're in the **SongsRepository**, the query already knows to select from that table the **song** part is the table alias- it's like in MySQL when we say **SELECT * FROM record g** in this case **g** is an alias we can use the rest of the query. In my case i chose the **song** alias.
To add **Where** clause, chain **->andWhere()** with **song.isPublished = :isPublished**. The **:isPublished** looks weird (it doesn't matter what we call)- it's a parameter, like a placeholder. To fill it in, add **->setParameter('isPublished', true);**. We always set variables like this using parameters to avoid SQL injection attacks. Never concatenate strings in a query.
To execute the query, add **->getQuery()** and then **execute()**:

```
src/RecUp/RecordBundle/Repository/SongsRepository.php

return $this->createQueryBuilder('song')
    ->andWhere('song.isPublished = :isPublished')
    ->setParameter('isPublished', true)
    ->getQuery()
    ->execute();
```

The query will always end with either **execute()** if we want an array of results - or **getOneOrNullResult()** - if we want just one result or null if nothing is matched.

**Using the Custom Query**

Using the new method is simple. Replcae **findAll()** with **findAllPublished()**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

public function listAction()
{
    $em = $this->getDoctrine()->getManager();

//        dump($em->getRepository('RecordBundle:Record'));die;

    $songs = $em->getRepository('RecordBundle:Record')
        ->findAllPublished();
```

Now refresh, and few songs are disappeared.

Links
-----

* [Go Pro with Doctrine Queries][23]

April 3, 2016 (adding custom Alice function, adding isPublished)
================================================================

To make the song names more realistic, we can use our own function:
```
src/RecUp/RecordBundle/DataFixtures/ORM/fixtures.yml

RecUp\RecordBundle\Entity\Record:
  record_{1..10}:
    songName: <songs()>

```
If we run this now we get this error:
```
Unknow formattter "songs"
```

 Faker calls the "formatters" function. We need to create our own formatter.

**Adding a Custom Formatter**

Each of the generator properties (like "name", "address", "lorem") are called "formatters". A [faker][21] generator has many of them, packaged in **providers**. We can easily override existing formatters, just add a provider containing method name after the formatters we want to override, or make our own.

In **loadeFixtures** in the **load()** function we can add a third argument - it's sort of an "options" array. Give it a key called **providers** - these will be additional objects that provide formatter functions - and set it to an array with **$this**:

```
src/RecUp/RecordBundle/DataFixtures/ORM/LoadFixtures.php

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(
            __DIR__.'/fixtures.yml',
            $manager,
            [
                'providers' => [$this]
            ]);
    }
}    
```

To add a new **songs** formatter, add **public function songs()**. And add some song names array to it. finish it with **$key = array_rand($names)** and then **return $names[$key]**:

```
src/RecUp/RecordBundle/DataFixtures/ORM/LoadFixtures.php

public function songs()
{
    $names = [
        'Rockin Anarchy',
        'Devastation Will Eat You',
        'White Lazer',
        'Raging Consequence',
        'Doubt Stabbed Me In The Back',
        'Stealing Lesbianism',
        'Satin Shadow',
        'Lock Up The Mother',
        'Fairness Overdose',
        'Sick Of The Shadow',
        'Bleeding Riff',
        'Violent Psycho',
        'Chrome Cigarette',
        'Feel That Firecracker',
        'Choking On Persuasion',
        'Crystal Strength',
        'Expensive Runaround',
        'Strange Waste',
        'Secret Loser',
        'Stoned Sin'
    ];

    $key = array_rand($names);

    return $names[$key];
}
```

Run:
```
php app/console doctrine:fixtures:load
```

### New random boolean column

To have the ability to have published and unpublished songs, we need a new property for that.

To cerate the new property make a new **private property** call it **$isPublished**, add the annotations and at the bottom add the setter function:

```
class Record
{
...
    /**
     * @ORM\Column(type="boolean")
     */
    private $isPublished = true;
...
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;
    }
}
```

We need to update the fixtures. but first, generate the migration:

```
php app/console doctrine:migrations:diff
```

This generate the new migration file. check the file and run the migrate command:

```
php app/console doctrine:migrations:migrate
```

Now we have the new field in the database and the values are all 0, so we nned to have few published by random. Faker to help again there is a **boolean()** function and we can add a value to it to have chance on getting the boolean value to 1 the variable is called [$changeOfGettingTrue][22]. so in the fixtures files, add **isPublished** and set that to **boolean(75)** (75%) - so that most songs are published:

```
RecUp\RecordBundle\Entity\Record:
  record_{1..10}:
    ...
    isPublished: <boolean(75)>
```

Run the fixtures:
```
php app/console doctrine:fixtures:load
```

Its working now, the values are changed some of the songs to 1, but we can see the songs with the **isPublished** value of 0 too, so next we need to make a custom query.



April 1, 2016 (dummy data using DoctrineFixturesBundle, and with **Alice**)
=======================================================

To make easily dummy data I will use the **DoctrineFixturesBundle** with this bundle we can quickly re-populate our local database with a really rich set of fake data, or fixtures.

The first step is to search for **DoctrineFixturesBundle** bundle and copy the **composer require** line, But also download: **nelmio/alice**. That's just a normal PHP library, not a bundle, more on this later.

```
composer require --dev doctrine/doctrine-fixtures-bundle nelmio/alice
```

The **--dev** flag means that these lines will be added to the **require-dev** section of **composer.json**. And that's meant for libraries that are only needed for development or to run tests.

Next add the **new** bundle line to the **AppKernel** but in the section that's inside of the **def if** statement:

```
class AppKernel extends Kernel
{
    public function registerBundles()
    {

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {

            $bundles[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

    }

}
```
This makes the bundle - and any services, commands, etc. that it gives us - not available in the **prod** environment. This is a development tool and this keeps the **prod** environment a little smaller.

**Creating the Fixtures Class**

This bundle gives us a new console command **doctrine:fixtures:load**. When we run that it'll look for "fixture classes" and run them. And in those classes, we'll create dummy data.

Make a new **DataFixtures/ORM** directory in the **RecUp/RecordBundle**, then add a new PHP class called **LoadFixtures** (it doesn't matter what we call). And paste the example class from the docs and update its class name to be **LoadFixtures**, but don't add the **User** code. We need to create songs, copy the code from **newAction()** and use the **$manager** instead of the **em** what we used in the **newAction()**, because the **$manager** argument passed to this function is the entity manager (**oBjectManager $manager**), don't forget the **Record use** statement.

```

namespace RecUp\RecordBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use RecUp\RecordBundle\Entity\Record;

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $record = new Record();
        $record->setSongName('the best of '.rand(1,100));
        $record->setArtist('Johnny');
        $record->setGenre('rock');


        $manager->persist($record);
        $manager->flush();
    }
}
```

Finally to run this, run in the terminal:

```
php app/console doctrine:fixtures:load
```
This clears out the database and runs all of the fixture classes.

The more easier and best way to add some dummy data is with **Alice**
---------------------------------------------------------------------

I installed the library called [nelmio/alice][19]. This library lets us add fixtures data via YAML files. It has an expressive syntax and it has a bunch of built-in functions for generating random data. It uses yet another library behinde the scenes called [Faker][20] to do that.

**Creating the Fixtures YAML file**

In the ORM directory create the nwe file **fixtures.yml** (it doesn't matter).

Start with the class name we want to create - **RecUp\RecordBundle\Entity\Record** next, each Record needs an internal unique name - it could be anything, but finish the name with **1..10**:

```
RecUp\RecordBundle\Entity\Record:
  record_{1..10}:
```
With this syntax, Alice will loop over and cerate 10 Record objects.

To finish things, set values on each of the Record properties. **songName: <text(20)>**. We can but any value here, but when use **<>**, we're calling a built-in Faker function, next add the other properties.

```
RecUp\RecordBundle\Entity\Record:
  record_{1..10}:
    songName: <text(20)>
    artist: <text(15)>
    genre: <text(5)>
    about: <sentence()>
```

To load this file, open the **LoadFixtures** and remove all the other code from load function. Replace it with **Fixtures** don't forget the use statement for **Nelmio\Alice\Fixtures** The **Fixtures** are from the **Alice** library and we are using the **load** function to get the path to our **fixtures.yml** file, add the directory path then the entity manager:

```

use Nelmio\Alice\Fixtures;

class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager);
    }
}
```

Run the doctrine command again
```
php app/console doctrine:fixtures:load
```

Now after refresh 10 completely random dummy songs data. Alice is easy but in the **Faker** library docs we can see all the built-in functions like (**numberBetween, word, sentence**) and much more.

Links:
-----

* [nelmio/alice][19]
* [Faker][20]



March 30, 2016 (Querying from songs not from hard coded data, and handle a 404 page)
====================================================================================

We have a song list page, and the show page lets link them together.

First add name (**record_show**) to the showAction route its to be easier in the future when we change the route URL, then we don't have to hunt down all the update every link on the site so for that we add this unique name to the route, and use the **name** instead.

```
src/RecUp/Controller/DefaultController.php

/**
 * @Route("/test/{track}", name="record_show")
 */
public function showAction($track)
{
  ....
}
```

Next in the **list.html.twig** add an **a** tag and use the **path()** twig function to point to the **record_show** route. Remember this route has a **{track}** wildcard, so we must pass value for that here. Finish with **track: song.songName**. And make sure to the text is still **song.songName**:

```
src/RecUp/Resources/views/song/list.html.twig

{% for song in songs %}
<tr>
  <td>
    <a href="{{ path('record_show', {'track': song.songName}) }}">
      {{ song.songName }}
    </a>
  </td>
  <td> {{ song.genre }}</td>
  <td> {{ song.updatedAt|date('Y-m-d') }} </td>
</tr>
{% endfor %}
```

Its going to work now but only the **songName** is going to be dynamically working the rest is the hardcoded stuff.

**Querying for One Song**

In the controller get rid of $funfact. We need to query from Record that matches the **track**. First fetch the entity manager:

```
src/RecUp/Controller/DefaultController.php


    public function showAction($track)
    {
        $em = $this->getDoctrine()->getManager();

    }

}
```

Then, **  $songs = $em->getRepository()** with the **RecordBundle:Record**. Next use the **findOneBy** method, this works by passing it an array of things to find by - in our case **'songName' => $track**:

```
$em = $this->getDoctrine()->getManager();

$songs = $em->getRepository('RecordBundle:Record')
    ->findOneBy(['songName' => $track]);
```

Comment out the caching fro now.

Finally, since we have a **Record** object, we can simplify the **render()** buy only passing the **'name' => 'songs'** (so we don't need to pass each value individually because we have the object).

```
return $this->render('@Record/Default/show.html.twig', array(
    'name' => $songs,
```

Next we need to change the variables passed into the **show.html.twig** template and add the other variables from the object (artist,genre,about), and remove the **raw** filter temporarily.

```
src/RecUp/Resources/views/Default/show.html.twig

{% block body %}
    <h2 class="genus-name">{{ name.songName }}</h2>

    <div class="sea-creature-container">
        <div class="genus-photo"></div>
        <div class="genus-details">
            <dl class="genus-details-list">
                <dd>{{ '99999'|number_format }}</dd>
                <dt>Subfamily:</dt>
                <dd>{{ name.artist }}</dd>
                <dt>Known Species:</dt>
                <dd>{{ name.genre }}</dd>
                <dt>Fun Fact:</dt>
                <dd>{{ name.about }}</dd>
            </dl>
        </div>
    </div>
  <div id="js-notes-wrapper"></div>
{% endblock %}
```

Next change in the javaScript to **'track' name.songName** because we use the findOneBy() method.

### Handling 404's

If somebody went to songs name that did not exist, we get a twig error. Because in (show.html.twig) on line 3 **name.songName** is null not a Record object(we pass to the name the Record object in the DefaultController). In the **prod** environment, this would be a 500 page. We want the user to see the 404 page, to make that go in to the DefaultController the **findOneBy()** method eather return one Record object or null. if it does not return an object throw **$this->createNotFoundException('song not found')**:

```
src/RecUp/RecordBundle/Controller/DefaultController.php

public function showAction($track)
{
$em = $this->getDoctrine()->getManager();

$songs = $em->getRepository('RecordBundle:Record')
    ->findOneBy(['songName' => $track]);

    if(!$songs) {
      throw $this->createNotFoundException('song not found');
    }
```
The message will only be show in dev mode not in prod.

In the **prod** environment the user will se the 404 error template page that we can customize.



March 27, 2016 (Query for a list of songs, page real with a template from query data)
=====================================================================================

To list all of the songs. Create **public function listAction()** give it a route path of **/songs**

```
    /**
     * @Route("/songs")
     */
    public function listAction()
    {

    }
```

To query we need the entity manager. Everything in Doctrine starts with the entity manager. Get it with **$em = $this->getDoctrine()->getManage()**

To make the query, we always start the same way **$songs = $em->getRepository()**. Pass the class name not the table name that you want to query from. This gives us a repository object, we can now query from songs table. We can use a bunch of useful methods on it **findAll()** and **findOneBy**. use **findAll()**.

```
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $songs = $em->getRepository('RecordBundle:Record')
            ->findAll();
    }
```
And now we have the query of the songs to test if it works add a **dump($songs);die;**.


### Make this page real with a template.

Return: **$this->render('@Record/song/list.html.twig')** and pass it a **songs** variable:

```
    /**
     * @Route("/songs")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $songs = $em->getRepository('RecordBundle:Record')
            ->findAll();

        return $this->render('@Record/song/list.html.twig', [
           'songs' => $songs
        ]);
    }
```

Now create a new file the **list.html.twig** in **RecordBundle/Resources/views/song/**. Extend the **base.html.twig** and ovveride the **body** block.

Since the **songs** is an array, loop over it and get the songName, genre. Notice that the **songName** and **genre** is a private property. But Twig behind the scenes, noticed that **songName** was private and called **getName()** instead. And it does the same thing with **song.genre**. Twig is smart enough to figure out how to access data and this lets us keep the template simple. We can add a third column to the table called "Last Updated". This wont work yet, but we need to say **{{ song.updatedAt}}**. If this existed and returned a DateTime object, we could pipe through the built in **date** filter format. But this won't work there is not an **updateAt** property. We can add later, but we can fake it. just add a **public function getUpdatedAt()** and return a random **DateTime** object. Twig doesn't care that there is no **updateAt** property it will call the getter function anyway.

```
{% extends 'base.html.twig' %}

{% block body %}

<table class="table table-striped">
    <thead>
    <tr>
        <th>Song Name</th>
        <th># of song</th>
        <th>Last updated</th>
    </tr>
    </thead>
    <tbody>
    {% for song in songs %}
        <tr>
            <td>{{ song.songName }}</td>
            <td>{{ song.genre }}</td>
            <td>{{ song.updatedAt|date('Y-m-d') }}</td>
        </tr>
    {% endfor %}
    </tbody>
    </table>
{% endblock %}
```



March 25, 2016 (Adding more columns to the record table, DoctrineMigrationsBundle)
==================================================================================

If we want to add new fields to the record table we need to create first the properties:

```

class Record
{
    private $artist;

    private $genre;

    private $about;
}
```

Now add the **Column** annotations above each:

```
class Record
{
/**
 * @ORM\Column(type="string")
 */
private $artist;

/**
 * @ORM\Column(type="string")
 */
private $genre;

/**
 * @ORM\Column(type="string")
 */
private $about;
}
```

If we need another field type we can find in the Doctrine types docs. The most common ones are **string**, **integer**, **text** and **float**.

Now create the getters and setters:

```

    /**
     * @return mixed
     */
    public function getArtist()
    {
        return $this->artist;
    }

    /**
     * @param mixed $artist
     */
    public function setArtist($artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    /**
     * @return mixed
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * @param mixed $about
     */
    public function setAbout($about)
    {
        $this->about = $about;
    }
```

And that's it. Create the properties, add the annotations and the getters and setters if you need them.

### Updating the Table schema

To update the **record** table the **NOT** safe way is to just run the:
```
php app/console doctrine:schema:update --force
```
command, but if we have the project deployed, then if we rename a property, then this command might drop the existing column and add a new one. All the data from the old column would be gone! The point is: runnig **doctrine:schema:update** is just too dangerous on production.

### Database Migrations

and for that we have database migration (the safe way). To install **DoctrineMigrationsBundle** run in terminal:

```
composer require doctrine/doctrine-migrations-bundle
```

Then add the **new** statement to the **AppKernel** class:

```
app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(

            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

        );

    }

}
```

### The Migrations Workflow

Our goal is to find a way to safely update our database schema both locally and on production. To do this right, drop the database entirely to remove all the tables: like we have a new project:

```
php app/console doctrine:database:drop --force
```

This is the only time you'll need to do this. Now re-create the database:

```
php app/console doctrine:database:create
```

Now, instead of running **doctrine:schema:update**, run:

```
php app/console doctrine:migrations:diff
```

This created a new file in **app/DoctrineMigrations**. The **up()** method in the file executes the exact SQL that we would have gotten from the **doctrine:schema:update** command. But instead of running it, it saves it into this file. This is our chance to look at it and make sure it's perfect, so we can change something if its wrong.

When we are ready, run the migration with:

```
php app/console doctrine:migrations:migrate
```

Done! When we deploy, we'll also run this command. But this command will only run the migration files that have not been executed before. Behind the scenes, this bundle crates a **migrations_versions** table that keep stack of which migration files it has already executed. This means we can safely run **doctrine:migrations:migrate** on every deploy: the bundle will take care of only running the new files.

### Making Columns nullable

In **newAction()**, add some code that sets fake data on **SongName**, **Artist**, **Genre** but leave the **About** blank:

```
public function newAction()
{
    $record = new Record();
    $record->setSongName('the best of '.rand(1,100));
    $record->setArtist('Lenny');
    $record->setGenre('rock');
  }
```

When we try in browser we got a huge error, that **About** cannot be null.
Doctrine configures all columns to be required in the database by default, if we want column to be "nullable", find the column and add **nullable=true**:

```
class Record
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $about;

}
```

**Creating Another Migration**

It's not makes our table automatically updated, so if we made this change we need another migration:

```
php app/console doctrine:migrations:diff
```

This creates the new migration file, we can see there **ALTER TABLE record CHANGE about** to have a default of **null**. This is what we want so execute the migration:

```
php app/console doctrine:migrations:migrate
```
Refresh the page and no errors!



March 24, 2016 (Inserting new objects using doctrine)
=====================================================

When we need to record new song we need a system to add that. We would have a URL like **record/new**. The user would fill out a form, hit submit, and the databse would insert a new record to the **record** table.

So create a **newAction()** with the URL **/record/new**:

```
class DefaultController extends Controller
{
    /**
     * @Route("/record/new")
     */
    public function newAction()
    {

    }

    /**
     * @Route("/test/{wat}")
     */
    public function indexAction($wat)
    {

    }

}
```

**BE Careful with Route Ordering!**

I put **newAction()** above **indexAction()**. Routes match from top to bottom. If i had put **newAction()** below **indexAction()**, going to **/test/new** would have matched **indexAction()** - passing the word "new" as the **wat**. To avoid this, put most generic-matching routes ner the bottom.
We can use [route requirements][18] to make a wildcard only match certain patters (instead of matching everything).

### Inserting a Record

we are not going to do with forms for now.

Instead, insert some hardcoded data. To start: **$record = new Record**(to make new Record object, also don't forget the **use statement for the Record object**), put some data on that object and tell Doctrine to save it:

```
class DefaultController extends Controller
{
    /**
     * @Route("/record/new")
     */
    public function newAction()
    {
        $record = new Record();
    }

}
```

Doctrine wants to stop thinking about queries, and instead think about objects.

In the **Entity/Record** we have the **name** the only real field we have. And it's a private property, To make mutable (changeable) make the getters and setters for the name:

```
class Record
{

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
}
```

Now go back to **DefaultController** and use **$record->setName** and call it **Lenny** and add some random number for the end if we make not just one.
We have the object populated with data now we need to tell Doctrine: I want you to save this to our record table.

Everything in Symfony is done with a service and Doctrine is no exeption: it has one magical service that saves and queries. it's called, the entity manager. It's has its own controller shortcut to get it: **$em = $this->getDoctrine()->getManager()**.

To save data, use two methods **$em->persist($record)** and **$em->flush**:

```
        $record = new Record();
        $record->setName('Lenny'.rand(1, 100));

        $em = $this->getDoctrine()->getManager();
        $em->persist($record);
        $em->flush();
```

We need both lines. The persist tells Doctrine that we want to save this. But the query isn't made until we call **flush()**. And we use these exact two lines whether we're inserting a new Record or updating an existing one. Doctrine figures out the right query to use.

### Finishing the New Page

The controller must always return the **Response** object. Skip the template for now and just **return new Response()**:
```
return new Response('song created!');
```
When we open in the web browser /record/new we see no errors but we are missing the web debug toolbar, to se if the query is goin through. That's because we don't have a full, valid HTML page - so go back to the controller and hack in some HTML markup into the response:
```
return new Response('<html><body>song created!</body></html>');
```
Try it again. now there is the fancy web debug toolbar. And there is actually three database queries. We can check the queries out by click the icon to enter the profiler. And there's the insert query, hiding inside a transaction, and we can see in 3 different format: formatted query, a runnable version, or run EXPLAIN on a slow query for debugging its great.

### Running SQL Queries  in the Terminal

We can even  check in the terminal using the doctrine query:

```
php app/console doctrine:query:sql "SELECT * from record"
```
Or in the phpmyadmin.


March 23, 2016 (Doctrine and the database, creating an entity class)
====================================================================

For fetching data from the database we are going to use [Doctrine][14]. Is Doctrine part of Symfony? No! Symfony doesn't care how or if you talk to database at all. You could use a direct PDO connection, use Doctrine, or do something else entirely. As usual, you're in control.

**Doctrine is an ORM**

ORM: Object Relation Mapper. In short, that means that every table - lke **record** - will have a corresponding PHP class that we will create. When you query, the **record** table, Doctrine will give you a **Record** object. Every property in the class maps to a column in the table. The main goal is mapping between a table and a PHP class in Doctrine.

### 1) Creating an Entity Class

Let's create a **record** table in the database and load all of this dynamically from there. We don't create a database table in Doctrine. Our job is to create a class, then Doctrine will create the table based on that class.

Create **Entity** directory in RecUp/RecordBundle and then create normal class inside called **Record**:

```
namespace RecUp\RecordBundle\Entity;

class Record
{
}
```

The **entity** is a class that Doctrine map to a databse table.

### 2) Configuration with Annotations!

To do that - Doctrine needs to know two things: what the table should be called and what columns it needs, and for that we are going to use annotations! Whenever we are using annoation, we need a **use* statement for it.

```
namespace RecUp\RecordBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
```

Every entity class will have the same **use** statement, next create **ORM Class**:

```
/**
 * @ORM\Entity
 * @ORM\Table(name="record")
 */
class Record
{

}
```
When you use annotations in the Doctrine we need to use the **@ORM** prefix for every Doctrine Mapping Types.
Doctrine now knows this class should map to a table called **genus**.

### Configuring the Columns

But that table won't have any columns yet, Add two properties **id** and **name**. To tell Doctrine that these should map to columns, use the **ORM Annotation**:

```
/**
 * @ORM\Entity
 * @ORM\Table(name="record")
 */
class Record
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $name;
}
```

The **id** columns is special - it will almost always look exactly like this: it basically says that **id** is the primary key.

The **type** option that's set to **string** is a Doctrine "type", and it will map to a **varchar** in MySQL. There are other [Doctrine types][15].

If we have the database configured in **parameters.yml** then we can run this command to create the databse
```
php app/console doctrine:databse:create
```

Make sure that XAMPP is running, and if we have the database then before we are executing the the tables run:
```
php app/console doctrine:schema:update --dump-sql
```

If we are satisfied run this command to to execute the query:
```
php app/console doctrine:schema:update --force
```
Now we can see in phpmyadmin the table that was created by doctrine.

links
-----

* [Doctrine Mapping Types][15]
* [Doctrine 2 ORM’s documentation][16]
* [21. Annotations Reference][17]

March 22, 2016 (kernel.cache_dir, kernel.root_dir, for what is parameters.yml)
==============================================================================

There are some special parameters in the big list **debug:container** gave us.
Notice **kernel.debug** - whether or not we're in debug mode - and **kernel.environment**. But the best one is the **kernel.cache_dir** - where Symfony stores its cache and **kernel.rooot_dir** - which is the **app/** direcotry where the **AppKernel** class lives. Anytime we need to reference a path in the project, use the **kernel.root_dir** and build the path from it.

We have cache path for the markdown cache in referencing absolute path, that's not a good practice. So let's use the **kernel.cache_dir**.
Just change the **directory** to **%kernel.cache_dir%** then **/markdown_cache**:

```
app/config/config.yml
doctrine_cache:
    providers:
        my_markdown_cache:
            type: %cache_type%
            file_system:
                directory: %kernel.cache_dir%/markdown_cache

```

Its ok to mix the parameters inside larger strings.
Clear the cache in the **prod** and in the **dev** environment:

```
php app/console cache:clear --env=prod    # prod
php app/console cache:clear               # dev  
```

And there's the cached markdown in **app/prod/**

### Why is parameters.yml Special?

**parameters.yml** holds any configuration that will be different from one machine where the code deployed to another.

For example, your database password is most likely not the same as my database password, but if we put that password right in the middle of **config.yml**, that would be a nightmare! Because if i commit my password to git and then you need to change it to your password but the not try to commit that change.

Instead of that confusing mess, we use parameters in **config.yml**. That allows to isolate the machine-specific configuration to **parameters.yml**. And here's the final key: **parameters.yml** is not committed to the repository - there's an entry for it in **.gitignore**:

```
/app/config/parameters.yml
```

If you just clone this project, the project won't have a **parameters.yml** file: you have to create it manually. Actually, this is the exact reason for this other file: **parameters.yml.dist**.
This is not read by Symfony, it's just a template of all of the parameters this project needs. If you add or remove things from the **parameters.yml**, be sure to add or remove them from **parameters.yml.dist**, because you commit this file to git.
>But when you install the project running the **composer install**, Symfony will read **parameters.yml.dist** and ask you to fill in any values that are missing from **parameters.yml**. So we actually then generating the **parameters.yml**

March 21, 2016 (parameters)
===========================

### Parameters: The Variables of Configuration

in the **config.yml** file: one of the settings - **default_locale** - is set to a strange-looking value: **%local%**:

```
app/config/config.yml
framework:

    default_locale:  "%locale%"
```

Scrolling up a bit, there's another root key called **parameters** with **locale: en**:

```
# Put parameters here that don't need to change on each machine where the app is deployed
# http://symfony.com/doc/current/best_practices/configuration.html#application-related-configuration
parameters:
    locale: en
```

It's called a power of a special "variable system" inside config files, in any of these configuration files, we can have a **parameters** key. And below that we can create variables like **locale** and set that to a value. We can reuse that value in any other file by saying **%locale%**.

In the **doctrine** key:

```
app/config/config.yml
# Doctrine Configuration
doctrine:
    dbal:

        host:     "%database_host%"
        port:     "%database_port%"
        dbname:   "%database_name%"
        user:     "%database_user%"
        password: "%database_password%"
```

We can see there is a bunch of these like **%database_host%** and **%database_port%**. These are just like **locale** but in a different file: **parameters.yml**:

```
# This file is a "template" of what your parameters.yml file should look like
# Set parameters here that may be different on each deployment target of the app, e.g. development, staging, production.
# http://symfony.com/doc/current/best_practices/configuration.html#infrastructure-related-configuration
parameters:
    database_host:     127.0.0.1
    database_port:     ~
    database_name:     symfony
    database_user:     root
    database_password: ~
```

So that's it, if you add a new key under **parameters**, you can use that in any other file by saying **%parameter_name%**.

And just like services, we can get a list of every parameter available. Run in the console:

```
php app/console debug:container --parameters
```

### Creating new Parameter

In the **prod** environment, we use the **file_system** cache. In **dev**, we use **array**. We can impore this.
Create a new parameter called **cache_type** and set that to **file_system**. Scroll down and set **type** to **%cache_type%**

```
app/config/config.yml
parameters:

    cache_type: file_system

doctrine_cache:
    providers:
        my_markdown_cache:
            type: %cache_type%
```

Now run over the terminal to see the new parameter that we created:

```
php app/console debug:container --parameters
```

It's there now clear the cache in the **prod** environment so we can check everything is still working:

```
php app/console cache:clear --env=prod
```

The main part or the meaning of this was that now in the **config_dev.yml** we can use less code just add **parameters** key from **config.yml** and chnage its value to **array** and then completely remove the **doctrine_cache** key at the tbottom:

```
 app/config/config_dev.yml
 parameters:
    cache_type: array # The array type is "fake" cache: it won't
                      # ever store anything
```

Its the same but with less code in the **dev** environment.

March 20, 2016 (config_dev.yml vs config_prod.yml, Caching the prod environment only)
=====================================================================================

### The **dev** and **prod** environments

We have two environments, compare **app.php** and **app_dev.php**. The important difference is a single line: **$kernel = new AppKernel()**. That's the class that lives in the **app/** directory.

The first argument to **AppKernel** is **prod** in **app.php**

```
web/app.php

$kernel = new AppKernel('prod', false);
```

And **dev** in **app_dev.php**

```
web/app_dev.php

$kernel = new AppKernel('dev', true);
```

This defines the environments. The second argument - **true** or **false** - is a debug flag and basically controls whether or not errors should be shown.

### config_dev.yml versus config_prod.yml

So the **dev** and **prod** strings are used when Symfony boots, and it loads only one configuration file for the entire system. The **dev** environment loads only **config_dev.yml** and **prod** loads **config_prod.yml**.

We can see the first lne of **config_dev.yml** or **config_prod.yml**

```
imports:
    - { resource: config.yml }
```

It imports the main **config.yml**: the main shared configuration. Then, It overrides any configuration that's special for the **dev** or **prod** environment.

In **config_dev.yml** under **monolog** - which is the bundle that gives us the **logger** service - it configures extra logging for the **dev** environment:

```
monolog:
    handlers:
        main:

            level:  debug
```

By setting **level** to **debug**, we're saying "log everything no matter its priority"

In **config_prod.yml** we have similar setup for the logger, but now it says **action_level: error**:

```
monolog:
    handlers:
        main:

            action_level: error

```

This only logs messages that are at or above the **error** leve. So only messages when things break.

**Using the firephp in confing_dev.yml**

In **dev** environment under **monolog**, uncomment the **firephp** line:

```
monolog:
    handlers:
        # uncomment to get logging in your browser
        # you may have to allow bigger header sizes in your Web server configuration
        firephp:
            type:   firephp
            level:  info

```

This handle will show log messages right in the browser. Make sure to install **FirePHP** extension in the browser, then enable it and go to the page "Inspect Element" after that refresh the page and we can evan see what route was mathed fro our ajax call. We don't want this to happen on production, so we only enabled this in the **dev** environment.


### Caching in the prod Environment Only

We need to cache our markdown processing, but what if we need to tweak how the markdown renders? In that case, we don't want caching. We need to disable caching in the **dev** environment only!

Copy the **doctrine_cache** from **config.yml** and paste it into **config_dev.yml**, change **type** from **file_system** to **array**:

```
doctrine_cache:
    providers:
        my_markdown_cache:
            type: array
```
The **array** type is a "fake" cache: it won't ever store anything. Now try, its working because it takes entire second of the **sleep()** if we try again same. In the **prod** still really fast.

### Clearing prod Cache

If we change in **config.yml** the **thousands_separator** back to comma:

```
# Twig Configuration
twig:
    number_format:
        thousands_separator: ','
```

In the **dev** environment we have no problems, but in the **prod** environment we have still a period.

So in the **prod** environment is primed for speed. And that means, when you change any configuration, you need to manually clear the cache before you'll see those changes.

To do that in the terminal, run:

```
php app/console cache:clear --env=prod
```

Even in the console script executes your app in a specific environment. By default IT USES THE **dev** ENVIRONMENT.
Now in **prod** should be a comma.

### The other Files: services.yml, security.yml

All of these configuration files are imported in the top of the **config.yml**:

```
imports:
    - { resource: parameters.yml }
    - { resource: security.yml }
    - { resource: services.yml }
```

The key point is that all of the files are just loading each other: it's all the sam system.

In fact, I could copy all of **security.yml**, paste it into **config.yml**, completely delete **security.yml** and everything would be fine. The only reason **security.yml** even exists is because it good to keep that stuff in tis own file. The same goes for **services.yml**


March 19, 2016 (Configuring the DoctrineCacheBundle Service, Environments)
==========================================================================

We have now new service, let's use it: **$cache = $this->get('')** and start typing **markdown**, and there's our service the IDE auto completes it.

```
$cache = $this->get('doctrine_cache.providers.my_markdown_cache');
```

The goal is to make sure the same string doesn't get parsed twice through markdown. To do that create a **$key = md5($funFact);**. To use the cache service add **if ($cache->contains($key)).** in this case, just set **$funFact = $cache->fetch($key);** Else we need to parse through Markdown. For test purpose add a **sleep(1);** to pretend like our markdown transformation is really long time, then parse the fun fact and finish with **$cache->save($key, $funFact);**

```
 $cache = $this->get('doctrine_cache.providers.my_markdown_cache');
    $key = md5($funFact);
    if ($cache->contains($key)) {
        $funFact = $cache->fetch($key);
    } else {
        sleep(1); // fake how slow this could be
        $funFact = $this->get('markdown.parser')
            ->transform($funFact);
        $cache->save($key, $funFact);
    }
```

The default place the cache goes in **app/cache** we have there a **doctrine** direcotry with **cache/file_system** inside. There is our cached markdown.

### Configuring the Cache Path

to configure the cache path rerun the command:

```
php app/console debug:config doctrine_cache
```

We can see a **directory** in **file_system**, so we need to add new **direcotry** key and set it to for example **/tmp/doctrine_cache** in config.yml:

```
doctrine_cache:
  providers:
    my_markdown_cache:
      type: file_system
      file_system:
        directory: /tmp/doctrine_cache
```

It should be slow the first time, and then super fast the second time.

**The big picture: bundles give you services, and those services can be controlled in config.yml . Every bundle works little bit different - but if we can understand this basic concept, we can figure out and do whatever configuration we need**


### Environments

If **config.yml** is so important then the question is what ar all of those other files - like **config_dev.yml**, **config_test.yml**, **parameters.yml**, **security.yml** and **service.yml**?

The answer is **Environments**. In Symfony, an environment is a set of configuration. Environments are also one of the most powerful features.

Think about it: an application is a big collection of code. But to get the code running it needs configuration. It needs to know what your database  password, what file your logger should write to, and what priority of messages it should bother logging.

### The dev and prod Environments

Symfony has two environments by default: **dev** and **prod**. In the **dev** environment your code is booted with a lot of logging and debugging tools. But in the **prod** environment, that same code is booted with minimal logging and other configuration that makes everything fast.

> There's a third environment called **test** for writing automated tests.

**app.php** versus **app_dev.php**

These are lives in **web** directory, which is the document root. This is the only directory whose files can be accessed publicly.

These two files - **app.php** and **app_dev.php** - are the keys. When we visit the app, we always executing one of these files. Since we're using the **server:run** built-in web server we're executing **app_dev.php**
The web server is preconfigured to hit this file.

That mean's when we go to **localhost:8000/test/watever** that's equivalent to going to **localhost:8000/app_dev.php/test/watever**. With that URL, the page still loads exactly before.

So to switch to the **prod** environment just copy that URL and change **app_dev.php** to **app.php**.
Now we are in the **prod** environment: same app, but no web toolbar or other dev tools, this is optimized for speed.

In production we won't have this ugly **app.php* in the URL: you'll configure the web server to execute that file when nothing appears in the URL.

Other than on your production server, we are always want to be in the **dev** environment.


March 18, 2016 (adding Cache Service)
=====================================

We're going to be rendering lot of for example markdown, and we don't want to do this on every request - I'ts just too slow. We need to cache the parsed markdown. Symfony have a bundle called **DoctrineCacheBundle** for that.

### Enabling DoctrineCacheBundle

1.) First to get the latest stable version of the bundle run:

```
composer require doctrine/doctrine-cache-bundle
```

2.) Enable the bundle

```
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Doctrine\Bundle\DoctrineCacheBundle\DoctrineCacheBundle(),
        );

        // ...
    }

    // ...
}
```

### Configure the Bundle

First we need to configure the bundle to find configuration for the bundle run:

```
php app/console debug:config doctrine_cache
```

**Configuring a Cache Service**

Our gol is to get a cache service we can use to avoid processing markdown on each request. When we added **KnpMarkdownBundle**, we magically had a new service. But this bundle, we need to configure each service we want.

Open **config.yml** and add **doctrine_cache**. Below that, add a **providers** key:

```
doctrine_cache:
    providers:
```

Next, the config has a **name** key. This **Prototype** comment above that is a confusing term that means that we can call this **name** anything we want. Let's make it **my_markdown_cache**:

```
doctrine_cache:
    providers:
        my_markdown_cache:
```

Finally, tell Doctrine what type of cache this is by setting **type** to **file_system**:

```
doctrine_cache:
    providers:
        my_markdown_cache:
            type: file_system
```

Now run in the terminal:

```
 php app/console debug:container markdown_cache
```

We hava new service called **doctrine_cache.providers.my_markdown_cache**.

Links:
------
* [Github - DoctrineCacheBundle github][12]
* [Documentation - DoctrineCacheBundle][13]


March 17, 2016
==============

### Control Center for Services (Config.yml)

To configure and control how the services behave, we have just one file: **app/config/config.yml**. One file is responsible for controlling everything from the log file to the database_password.

in **config.yml** other then **imports** - which loads other files and **parameters**. Every root key in this file like **framework, twig and doctrine** - corresponds to a bundle that is being configured. For example:
All of stuff under **framework** is configuration for the **FrameworkBundle**.
Everything under **twig** is used to control the behavior of the services from **TwigBundle**.
The job of a bundle is to give us services. And this is our chance to tweak how to those services behave.

### Get a big list of all Configuration

We can use the documentation in the symfony.com website there is everything we can control for each bundle.

But we can see what Bundles we have and all of there configuration. first run this in terminal **php app/console debug:config** to see all the Bundles and there 'alias' names, after that re-run the command with an argument (the alis of one of the Bundles) like **twig** so
```
php app/console debug:config twig
```

Its going to dump a yml example of not everything we can configure but usually enough to find what we need under the **twig** key.

### Changing the Configuration

If we add a random big number like **99999** and sent through a bult in filter called **number-format**

```
index.html.twig
.....
 {{ '99999'|number_format }}
....
```

Then when we refresh the filter gives us a **99,999**, formatted-string. But if we live in a contry that formats using a **.** instead we need to change this in twig bundle service.

In **debug:config twig** dump, there's a **number_format**, **thousands_separator** key. In **config.yml** add **number_format** then **thousands_separator: '.'**

```
app/config/config.yml

# Twig Configuration
twig:

    number_format:
        thousands_separator: '.'

```

Behind the scenes, this changes how the service behaves, and now e have **99.999**.

So we can control virtually every behavior  of any service in Symfony. And since everything is done with a service, that makes us powerful.

**IF** *we make a typo we get a huge error. All of the configuration is validated. if we make a type, Symfony has our backs.



March 16, 2016 (2nd Part symfony, installing a Bundle to get more Services in **container**, using the markdown service)
========================================================================================================================

### The 2 Parts of Symfony.

The first part of the Symfony is: create a **route**, create a **controller** function, make sure the function returns a **Response**, So **Route->Controller->Response** and that's half of the Symfony.

The second half of Symfony is all about the huge optional useful objects that can help the work done. For example, there's a logger object, a mailer object, and a templating object. The **$this->render()** shortcut we've using in the controller is just a shortcut to go out to the **templating** object and call a method on it.
All of these useful objects - or services - are put into one big beautiful object called container. If We have the container, then we can fetch any object we want and do anything.

To see what handy services are inside of the container use the **php app/console debug:container** command.

### But Where do Services Come From?

In **app/AppKernel.php**

The kernel is the heart of the Symfony application, but it doesn't do much. It's main job is to initialize all the bundles we need. A bundle is basically just a Symfony plugin, and its main job is to add services to our container.
When we use the debug:container command, that giant list is provided to us from one of these bundles.

>But the simplest explanation: a bundle is basiacally just a directory full of PHP classes, configuration and other goodies.
We have our own: **RecordBundle**

### Install a Bundle: Get more Services

If we don't find the service that we need like a markdown parsing service, we have the symfony community to the rescue! In this case, there is: it's called **KnpMarkdownBundle**.
To get this bundle go to [KnpMarkdownBundle][11].
Copy its **composer require** line.
```
    composer require knplabs/knp-markdown-bundle
```
To enable the bundle, grab the **new** statement fomr the docs and paste that into **ApKernel** the order of these doesn't matter
```
$bundles = array(
    // ...
   , new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle()
);
```
That's it!
To test it. Try rinning **debug:container** again with a search for **markdown**

```
php app/console debug:container markdown
```
We have two services matching. These are coming from the bundle we just installed.

### Using the markdown Service

First remove the text in twig that we want to use the service on. 2nd add the removed text to a variable in the container.
```
  public function indexAction($wat)
 {
     $funFact = 'Octopuses can change the color of their body in just *three-tenths* of a second!';
     ........
 }
```

Add some asterisks around **three-tenths** Markdown should eventually turn taht into italics.

Pass **funFact** into the template:

```
....
 return $this->render('RecordBundle:Default:index.html.twig', array(
        'name' => $wat,
        'funFact' => $funFact,
    ));
```

Then render it with the normal **{{ funFact }}** in twig.

When we refresh the browser, we have the exact same text, but with the unparsed asterisks. We need to use the **markdown.parser** service to turn those asterisks into italics.

### Fetch the Service and Use it!

We have access to the container from inside a controller. So start with **$funFact = $this->container->get('markdown.parser')**. Now have the parser object and can call a method on it. The one we want is **transform()** pass that string to parse:

```
     $funFact = $this->container->get('markdown.parser')
            ->transform($funFact);
```

So we know the object has a **transform()** method because of the documentation of the bundle.

When we try it's not going to work exactly because the HTML tags are being escaped into HTML entities.
This is Twig by default, because twig one of the best features is that it automatically escapes any HTML we render. That gives us free security from XSS attacks(Cross-Site Scripting (XSS) attacks are a type of injection, in which malicious scripts are injected into otherwise benign and trusted web sites. XSS attacks occur when an attacker uses a web application to send malicious code, generally in the form of a browser side script, to a different end user). And for those few times when we want to print HTML, just add the **|raw** filter:

```
 {{ funFact|raw }}
```

Now it's rendering the italics.

**Fetching Services the lazy way**

We can fetch the services easier with less code. In the controller, replace **$this->controller->get()** with just **$this->get()**. This does the same thing as before.

March 15, 2016 (ReactJs talks to my API)
========================================

### Page-Specific JavaScript(or CSS)

The script tags that live in a **javascripts** block, we can override that block **{% block javascripts %}** then **{% endblock %)**. When we override them we override them completely. The solution to this is the **parent()** function. This prints all of the content from the parent block, and then we can put our stuff below that.

### Including the ReactJs Code

We need some JavaScript that will make an AJAX request to the notes API endpoint and use that to render them with the same markup we had before. We'll use ReactJs to do this. First we need to include three external script tags for React itself, and one more that points to our project.

```

{% block javascripts %}
    {{ parent() }}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.14.3/react-dom.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js"></script>
    <script type="text/babel" src="{{ asset('js/notes.react.js') }}"></script>
{% endblock %}
```

### The ReactJS App

This is a small ReactJS app that uses our API to build all of the same markup that we had on the page before dynamically. It uses jQuery to make the AJAX call (**$.ajax({...});**).
Back in the template, we need to start up the ReactJS app. Add a **script** tag with **type="text/babel** that's a React thing. To boot the app add **ReactDOM.render** and Render the **NoteSection** into **document.getElementById('js-notes-wrapper')**

```
 <script type="text/babel">
    ReactDOM.render(
          <NoteSection />,
          document.getElementById('js-notes-wrapper')
        );
   </script>
```

In the HTML area, clear things out and add an empty div with the same **id**.

```
<div id="js-notes-wrapper"></div>
```

Now refresh, It's working we can try to delete one comment in the controller to see if dynamically is changing (its checks for new comments every two seconds).
We have hardcoded URL right now we need to change that.

### Generating the URL for JavaScript

in the **notes.react.js** we change the value for the url to **this.props.url**:

```
var NoteSection = React.createClass({

    loadNotesFromServer: function() {
        $.ajax({
            url: this.props.url,

        });
    },

});


```

This means that we will pass **url** property to **NoteSection**. Since we create that in the Twig template, we'll pass it in there.

First, we need to get the URL to the API endpoint. Add **var notesUrl = ''**. Inside generate the URL with twig using **path()** pass it **record_show_notes**, and the **wat** set to name:

```
{% block javascripts %}

    <script type="text/babel">
        var notesUrl = '{{ path('record_show_notes', {'wat': name}) }}';

    </script>
{% endblock %}


```
Yes its Twig inside of JavaScript, and yes it's going to work.
Finally, pass this into React as a prop using **url={notesUrl}**

```

        ReactDOM.render(
          <NoteSection url={notesUrl} />,
          document.getElementById('js-notes-wrapper')
        );


```

And it's the same but we have dynamical URL not that ugly hardcoded one.

Links:
------
 * [For generating URLs purely fomr JavaScript][10]  

March 13, 2016 (Generating URLs)
================================

To put the URL to the **getNotesAction** page. Fill this **/test/{{ name ]}/notes** in the new anchor tag in the **index.html.twig** page. It's working but it's **WRONG**, this will only work if we don't change the URL for this route, because we then need to hunt down all the update every link on the site.
Instead, routing has a second purpose: the abillity to generate the URL to a specific route. But to we need to give the route a unique name. After the URL, add comma and **name="record_show_notes"**

```
   /**
     * @Route("/test/{wat}/notes", name="record_show_notes")
     * @Method("GET")
     */
    public function getNoteAction()
    .......
```

The name can be anything, but it's usually underscored and lowercased.

### The Twig path() Function

To generate URL in Twig, use the **path()** function. This has two arguments. The first is the name of the route - **record_show_notes**. The second, which is optional, is an associative array. In Twig, an associative array is written using **{ }**, just like JavaScript or JSON. Pass in the values for any wildcards that are in the route. This route has **{wat}**, so pass it **wat** set to the **name** variable:

```
 <a href="{{ path('record_show_notes', {'wat': name}) }}">Json Notes</a>
```

This generates the same URL, but if we ever need to change the URL for the route, all the links would automatically update if we have a **unique** name in the controller route options and we call that with the **path()** function in thw twig file.



March 12, 2016 (JSON Response)
==============================

### Creating API Endpoints

Symfony is always returns a **Response** but Symfony doesn't care whether that holds HTML, JSON, or CSV.
Create a new controller like **getNotesAction()**. This will return notes. Use **@Route("/test/{wat}/notes")**. The endpoint will be used for **GET** request to this URL. Add **@Method("GET")** when you type method don't forget when you use annotations, let PhpStorm autocomplete them for you. That's important because when you do that, PhpStorme adds a **use** statement at the top of the file that you need. If you forget this, you'll get an error about it.
After that check the route if its working **php app/console debug:router**.

### The JSON Controller

Create **$data** variable, set it to an array, and put the **$notes** in a **notes** key inside that. Its for JSON structure.
Finally return **$data** as JSON **return new Response(json_encode($data));** its going to work.
But we can make it easier. Replace the Response with **return new JsonResponse($data);**.
This does two things. First it calls **json_encode()** for you. And second, it sets the **application/json Content-Type** header on the Response, which we can set manually but with this is easier.


March 11, 2016 (Loading CSS & JS Assets)
========================================

The **web/** direcotry is the document root. In other words, anything in **web/** can be accessed by the public. If you want load up the **favico.ico**, just use like **http://localhost:8000/favicon.ico**. If a file is outside of the web, then it's not publicly accessible.

### Including Static Assets

To add assets we just include CSS and JS files the way we always do, with tru **link** and **script** tags. These paths are relative to the **web/** directory, because that's the document root.
The **stylesheets** and **javascripts** blocks are not doing much but in the future, we have the power to add page-specific CSS and JS by adding more **link** tags to the bottom of the **stylesheets** or the **javascripts** block from inside a child template.

```
 {% block stylesheets %}
            <link rel="stylesheet" href="{{ asset('vendor/bootstrap/css/bootstrap.min.css') }}">
            <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
            <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/font-awesome.min.css') }}">
        {% endblock %}
```

```
        {% block javascripts %}
            <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
            <script src="{{ asset('js/main.js') }}"></script>
        {% endblock %}
```

### The asset function

the **asset()** function is Whenever you refer to a static file, you'll wrap the path in **{{ asset() }}**. This in general don't do anything **BUT** if we deploy and use a **CDN**, it will save time. With just one tiny config change, Symfony can prefix every static URL with your CDN host. So **/css/style.css** becomes **http://somecdn.com/css/style.css**. So use **asset()** in case you need it. You can also do some cool cahce-busting stuff to make the page download your asset (generating random number before the file name, for more later.).

March 10, 2016 (Twig)
=====================

### Twig

Twig has two syntaxes: **{{ }}** - whic is the "say something" tag - and **{% %}** - whic is the "do something" tag.
```
{{ SaySomething }}, {% DoSomething %}
```

If you're printing something, you always write **{{** then a variable name, string or any expression: Twig looks a lot like javaScript.

If you're writing code that won't print something - like an **if** statement a **for** loop, or setting a variable you'll use {% %}.
In the Twig's website at [twig.sensilabs.org][9] click the Documentation and scroll down, there is a list of everything Twig does.

**The dump() Finction**

Create **$notes** variables in the controller with some text and pass into our Twig template. But before loop over, there is a function called **dump()**. This is like **var_dump()** in PHP, but better, and you can use it without any arguments to print details about every available variable.
```
{{ dump() }}
```

**The for Tag**

To print out the notes, add a **ul** and open up a **for** tag with **{% for note in noites**. Close it with an **{% endfor $}**

```
<ul>
    {% for note in notes %}
        <li>{{ note }}</li>
    {% endfor %}
</ul>
```

### {% twig_layouts %}

To get a leyout, add a new do something tag at the top of **index.html.twig: extends 'base.html.twig'**:
After that the page will not work because in the **base.html.twig** we have **blocks** and we need to use these blocks (rewrite). You can rename them and you can have as many as you need.
So add body block for our content and change the override the title block too, the order of block doesn't matter: this could be above or below the body.  

```
{% extends '::base.html.twig' %}
{% block title %} {{ name }} {% endblock %}
{% block body %}
<h1>Hello {{ name }}!</h1>

<ul>
    {% for note in notes %}
        <li>{{ note }}</li>
    {% endfor %}
</ul>

{% endblock %}
```

**Template name and path explanation/examples**

the **::base.html.twig** filename using the exact same syntax as the controller.
 Remember that template name always has trhee parts:
 * the bundle name
 * a subdirecotry
 * and the template filename

 In this case the bundle name and subdirecotry are just missing. When a template name has the bundle part, it means the template lives in the **Resources/views** directory of that bundle. But when this part is missing, like here, it means the template lives in the **app/Resources/views** direcotry. And since the second part is missing too, it means it lives directly there, and not in a subdirectory.

 **Examples**:

 * **RecordBundle:Default:index.html.twig**
 src/RecUp/RecordBundle/Resources/views/Default/index.html.twig
 * **RecordBundle::index.html.twig**
 src/RecUp/RecordBundle/Resources/views/index.html.twig
 * **::base.hml.twig**
 app/Resources/views/index.html.twig

March 9, 2016 (service controller, rendering the twig template, rendering template shortcut)
============================================================================================

### Service Container

To keep track off all of the services, Symfony puts them into one big associative array called the container. Each object has a key - like **mailer**, **logger** or **templating**. The container is actually an object. But think of it like an array: each useful object has an associated key. If I give you the container, you can ask for the **logger** service and it'll give you that object.
The first half of Symfony: **route-controller-response**. The second half of Symfony is all about finding out what objects are available and how to use them. We can evan add our own service objects to the container.

**Example**: In **RecordBundle** controller render the index.html.twig page.

**Accessing the Container**

We need the first object **templating** service: it renders the Twig templates. To get acces to the service container, we need to extend Symfony's base controller.

```
class DefaultController extends Controller
```

To get the **templating** service, add ``` $templating = $this->container->get('templating'); ```

The container pretty much only has one method: **get**. Give it the nickname to the service and it will return that object. it's super simple.

**Rendering a Template**

With the templating object we can render a template! Add ``` $html = $templating->render('')``` followed by the name of the template.

```
<?php

namespace RecUp\RecordBundle\Controller;
  ...

class DefaultController extends Controller
{
  ...
    public function indexAction($wat)
{
    $templating = $this->container->get('templating');
    $html = $templating->render('RecordBundle:Default:index.html.twig', array(
        'name' => $wat
    ));

    return new Response($html);
}
}

```

We can pass variables like ``` 'name' => $wat ``` and finally what we always have to do return a Symfony's **Response** ojbect ```return new Response($html)```

**Create the Template**

go to src/Recup/RecordBundle/Resources/views/Default/index.html.twig

```
<h1>Hello {{ name }}!</h1>

```

Just pass the variable what we define in the controller ( the 'name' => $wat).

### Rendering template shortcut

> Rendering a template is pretty common, so there's a shortcut when you'r in a controller. Replace all of the previous code with a simple **return $this->render**

```
class DefaultController extends Controller
{
    ...
    public function indexAction($wat)
{
    return $this->render('RecordBundle:Default:index.html.twig', array(
        'name' => $wat
    ));
}
}
```

> That's it. The render function is simply goes out to the **templating** service - just like we did - and calls a method named **renderResponse()**. This method is like the **render()** function we called, exept that it wraps the HTML in a Response object for convenience.

> So the base **Controller** class has a lot of shortcut methods that we can use. But behind the scenes, these don't activate some weird, core functionality in Symfony. Instead, everything is done by one of the services in the container. Symfony doesn't really do anything: all the work is done by different services.

**What Services are there?**

> To find out what other services are hiding in the container use the console:

```
php app/console debug:container
```
> There are over 200 useful objects in Symfony that you have access to out of the box. In the symfony docs, you'll find out which ones are important to you and your project.

> We can search from the list for the example if we wan't to know if ther is service for logging:

```
php app/console debug:container log
```

> There is 17, but usually what we want is the one with the shortest name(16 logger). This command also shows you what class you'll get back, which you can use to find the methods on it just type the number what you see before the service name in this case 16.


Links:
-----
  * [Service Container][8]

March 8, 2016 (Routing)
=======================

There are 2 ways (without annotations and with annotations) to handle routing in symfony.

**Example:** Creating homepage route. We just want to link path **/** to an action of our **HomeController**
  **1. Without annotations**

In **app/config/routing.yml**:

```
front_homepage:
    path: /
    defaults: { _controller: BlogFrontBundle:Home:index }
```
In **src/Blog/FrontBundle/Controller/HomeController.php**:

```
<?php
namespace Blog\FrontBundle\Controller;

class HomeController
{
    public function indexAction()
    {
        //... create and return a Response object
    }
}
```

In **routing.yml**, we declared a simple configuration for the route named **front_homepage** with 2 parameters: the path and the action of the controller we want to target. In the controller it doesn't need anything more or special here. In the **HomeController.php** we just return the Response object from the indexAction.

 **2. With annotations**

In **app/config/routing.yml**:

```
blog_front:
    resource: "@BlogFrontBundle/Controller/"
    type:     annotation
    prefix:   /
```
In **src/Blog?FrontBundle/Controller/HomeController.php**

```
<?php

namespace Blog\FrontBundle\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController
{
    /**
     * @Route("/", name="blog_home_index")
     */
    public function indexAction() { /* ... */ }
}
```

In **routing.yml**:
* **resource** targets the controller to impact
* **type** defines the way we declare routes
* **prefix** defines a prefix for all actions of controller class (optional)

But the magic is in the controller. Before everything else, we have to call the relevant class of the **SensioFrameworkExtraBundle**: **use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;**. Now we can implement the route and the parameters we want to assign: in this case only the path and the name: **@Route("/", name="blog_homepage")**.

### Debugging Routes

To see all URLs in the app:

```
php app/console router:debug
```

### Returning a Response

Controller functions are simple, and there's just one big rule: it must return a Symfony **Response** object.


Links:
------
  * [@Route and @Method][5]
  * [Getting Started with Symfony2 Route Annotations][6]
  * [Returning a JSON Response][7]


March 7, 2016 (The Console, Generating Bundles)
===============================================

### The Console

> We can create bundles manually, but we can use **console** instead, to see all the tricks it knows:

```
php app/console
```

### Generating the RecordBundle

Run the **generate:bundle** command:

```
php app/console generate:bundle
```

Say yes to sharing bundle across multiple applications, for the bundle namespace, type **RecUp/RecordBundle**. A bundle namespace always has two parts: a vendor name and a name describing the bundle.
Next add a nickname for our bundle, **RecordBundle**. The only rule is that it must ends with **Bundle**.
Use the target default directory, and choose **yml** as the configuration format.

### What the Generator Did

> This did three things.
> First, it made a **src/RecUp/RecordBundle** directory whit some sample bundle files.
> Second, it plugged the bundle into the motherboard by adding a line in **AppKernel** class.
> Third, it added a line to the **routing.yml** file that imports routes from the bundle.


March 6, 2016 (Build the first page)
====================================

> Code goes in src/ and app/. The src/ will hold all the PHP classes you created and app/ will hold everything else: mostly configuration and template files.

### Building the First Page

> Creating a page in Symfony - or any modern framework - is two steps: a route and a controller. The route is a bit of configuration that says what the URL is. The controller is a function that builds that page.

Example:

```
<?php

namespace AppBundle\Controller;


use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class GenusController
{
    /**
     * @Route("/genus")
     */
    public function showAction()
    {
        return new Response('Under the Sea!');
    }

}

```

The GenusController is a controller, the function that will (eventually) build the page. To create the route, use annotations: a comment that is parsed as configuration (@Route). Now just go to the **/genus** in browser.
 The only rule for a controller is that it must return a Symfony **Response** object.

<!-- links -->
[1]:https://symfony.com/what-is-symfony
[2]:https://knpuniversity.com/screencast/symfony/start-project
[3]:https://symfony.com/download
[4]:https://getcomposer.org/download/
[5]:https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/routing.html
[6]:http://www.sitepoint.com/getting-started-symfony2-route-annotations/
[7]:https://symfony.com/doc/2.8/components/http_foundation/introduction.html#creating-a-json-response
[8]:https://symfony.com/doc/2.8/book/service_container.html
[9]:http://twig.sensiolabs.org/
[10]:https://github.com/FriendsOfSymfony/FOSJsRoutingBundle
[11]:https://github.com/KnpLabs/KnpMarkdownBundle
[12]:https://github.com/doctrine/DoctrineCacheBundle
[13]:https://symfony.com/doc/current/bundles/DoctrineCacheBundle/index.html
[14]:http://www.doctrine-project.org/
[15]:http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/basic-mapping.html#doctrine-mapping-types
[16]:http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/index.html
[17]:http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html
[18]:https://symfony.com/doc/current/book/routing.html#adding-requirements
[19]:https://github.com/nelmio/alice
[20]:https://github.com/fzaninotto/Faker
[21]:https://github.com/fzaninotto/Faker#formatters
[22]:https://github.com/fzaninotto/Faker#fakerprovidermiscellaneous
[23]:https://knpuniversity.com/screencast/doctrine-queries
[24]:https://symfony.com/doc/current/bundles/SensioFrameworkExtraBundle/annotations/converters.html
[25]:http://docs.doctrine-project.org/en/latest/reference/unitofwork-associations.html
[26]:https://doctrine-orm.readthedocs.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html
[27]:http://www.doctrine-project.org/api/common/2.1/class-Doctrine.Common.Collections.ArrayCollection.html
[28]:https://symfony.com/doc/current/components/dependency_injection/introduction.html
[29]:https://symfony.com/doc/current/reference/dic_tags.html#twig-extension
[30]:http://twig.sensiolabs.org/doc/advanced_legacy.html#creating-an-extension
[31]:https://symfony.com/doc/current/cookbook/templating/twig_extension.html
[32]:https://www.digitalocean.com/community/tutorials/how-to-use-git-branches
[33]:https://symfony.com/doc/2.8/cookbook/frontend/bower.html
[34]:http://bower.io/
[35]:https://blog.engineyard.com/2014/frontend-dependencies-management-part-1
[36]:http://andy-carter.com/blog/a-beginners-guide-to-package-manager-bower-and-using-gulp-to-manage-components
[37]:https://www.ekreative.com/blog/setting-up-symfony2-with-gulp-and-bower-instead-of-the-assetic-bundle
[38]:https://knpuniversity.com/screencast/gulp/first-gulp
[39]:http://gulpjs.com/plugins/
[40]:https://www.npmjs.com/package/gulp-sass/
[41]:https://www.npmjs.com/package/gulp-sourcemaps
[42]:https://github.com/floridoo/gulp-sourcemaps/wiki/Plugins-with-gulp-sourcemaps-support
[43]:https://www.npmjs.com/package/gulp-concat/
[44]:https://www.npmjs.com/package/gulp-clean-css/
[45]:https://www.npmjs.com/package/gulp-util
[46]:https://www.npmjs.com/package/gulp-if/
[47]:https://stackoverflow.com/questions/10467475/double-negation-in-javascript-what-is-the-purpose
[48]:https://github.com/osscafe/gulp-cheatsheet
[49]:https://www.npmjs.com/package/gulp-plumber
[50]:https://www.npmjs.com/package/gulp-uglify/
[51]:https://www.npmjs.com/package/gulp-rev/
[52]:https://www.npmjs.com/package/del
[53]:https://www.npmjs.com/package/q
[54]:https://www.npmjs.com/package/gulp-autoprefixer
[55]:https://github.com/postcss/autoprefixer
[56]:https://symfony.com/doc/2.8/components/dependency_injection/types.html
[57]:https://github.com/FriendsOfSymfony/FOSUserBundle
[58]:https://github.com/excelwebzone/EWZRecaptchaBundle
[59]:https://symfony.com/doc/master/bundles/FOSUserBundle/index.html
<!-- / end links-->
