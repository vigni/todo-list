<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstName("Lucas");
        $user->setLastName("Duroyon");
        $user->setBirthdate(new \DateTime("29-06-1998"));
        $user->setPassword("passwordtest");
        $user->setEmail("lucas.sry@gmail.com");

        $manager->persist($user);

        $user1 = new User();
        $user1->setFirstName("Nico");
        $user1->setLastName("Dupont");
        $user1->setBirthdate(new \DateTime("02-10-1990"));
        $user1->setPassword("passwordtest");
        $user1->setEmail("dupont@gmail.com");

        $manager->persist($user1);

        $manager->flush();
    }
}
