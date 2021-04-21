<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;

class Fixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

//        $user = new User();
//        UserPasswordEncoder $passwordEncoder = new UserPasswordEncoder();
//
//        $user->setEmail('user@mail.com');
//        $user->setPassword($this->passwordEncoder->encodePassword('user'));
//        $user->setRoles('ROLE_USER');
//
//        $admin = new User();
//
//        $admin->setEmail('admin@mail.com');
//        $admin->setPassword($this->passwordEncoder->encodePassword('admin'));
//        $admin->setRoles('ROLE_ADMIN');

        $manager->flush();
    }
}
