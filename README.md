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

Important changes:
=================

* **March 30**:
  - Changing indexAction (index.html.twig) to showAction (show.html.twig)
  - Changing {wat} to {track}

April 8, 2016
=============

Can I save a **RecordComment** without setting a **Record** on it? No! I need to make that false. Go to **ManyToOne** annotation and add a new annotation below it: **JoinColumn**. Inside set **nullable=false**:

```
src/RecUp/RecordBundle/Entity/RecordComment.php

    /**
     * @ORM\ManyToOne(targetEntity="Record"))
     * @ORM\JoinColumn(nullable=false)
     */
    private $record;
```
The **JoinColumn** annotation controls how the foreign key looks in the database. It's optional. Another option is **onDelete**: that changes the **ON DELETE** behaivor in the database- the default is **RESTRICT** but we can also use **CASCADE** or **SET NULL**.

Before we make the migration we need to drop the database and then re-create it, and re-migrate from the beginning, because we have bunch of existing **RecordComment** rows in the database, and each still has a **null record_id**. I can't set that column to **NOT NULL** because of the data that's already in the database. (If the app were already deployed to production, we would need to fix the migration: maybe UPDATE each existing record_comment and set the record_id to the first record in the table.).

```
php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:migrations:migrate
```

The last step is to fix the broken fixures. We need to associate each **RecordComment** with a **Record**. In alice it's easy: use **record: @** then the internal name of one of the record - like **record_1**. But we can make to match this by random each time change to **record_**:

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
<!-- / end links-->
