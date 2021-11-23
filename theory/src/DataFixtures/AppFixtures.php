<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i=0; $i < 10; $i++) {
            $user = new User();
            $user->setName('Doot-' . $i);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
