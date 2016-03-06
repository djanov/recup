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

March 6, 2016
=============

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
<!-- / end links-->




