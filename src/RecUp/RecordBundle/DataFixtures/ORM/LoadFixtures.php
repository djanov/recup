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
//use RecUp\RecordBundle\Entity\Record; don't need if we use Alice


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
}
