<?php

namespace App\DataFixtures\Jira;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use App\Entity\Jira\Instance;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $instance = new Instance();
        $instance->setName('4xxi');
        $instance->setBaseUrl('https://fourxxi.atlassian.net');
        $instance->setToken('token');
        
        $manager->persist($instance);

        $manager->flush();
    }
}
