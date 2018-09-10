<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class Users extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //Creation de plusieurs utilisateurs
        for ($i=0; $i < 5 ; $i++) { 
            $user = new \App\Entity\Users($i);//on force car Users fixture et Users entity ont les même noms
            $user ->setName("name".$i)
                ->setLastName("lastname".$i)
                ->setEmail("mail".$i."@users.fr")
                ->setPassword("pass".$i)
                ->setDateCreate(new \DateTime());
            $manager->persist($user);
        }
        $manager->flush();
    }
}
