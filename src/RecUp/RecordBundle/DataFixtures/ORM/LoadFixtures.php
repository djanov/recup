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