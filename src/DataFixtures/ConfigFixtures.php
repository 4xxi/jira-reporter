<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Config;

class ConfigFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $config = new Config();
        $config->setKey('holidays');
        $config->setValue('*-01-01, *-01-02, *-02-23, *-03-08, 2018-03-09, *-05-01');
        $config->setHelp('Holidays for reports');
        
        $manager->persist($config);
        $manager->flush();
    }
}
