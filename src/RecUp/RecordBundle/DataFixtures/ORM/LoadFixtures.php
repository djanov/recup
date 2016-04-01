<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 4/1/2016
 * Time: 10:09 PM
 */

namespace RecUp\RecordBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\Fixtures;
//use RecUp\RecordBundle\Entity\Record; do't need if we use Alice


class LoadFixtures implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $objects = Fixtures::load(__DIR__.'/fixtures.yml', $manager);
    }
}
