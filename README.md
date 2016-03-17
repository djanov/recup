the project RecUp
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
<!-- / end links-->




