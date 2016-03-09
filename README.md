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

March 9, 2016 (service controller, rendering the twig template)
===============================================================

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
<!-- / end links-->




