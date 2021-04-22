<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use function Sodium\add;

class Fixtures extends Fixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load($manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();

        $user->setEmail('user@mail.com');
        $hashUser = $this->container->get('security.password_encoder')->encodePassword($user,'user');
        $user->setPassword($hashUser);
        $rolesUser = array();
        array_push($rolesUser, 'ROLE_USER');
        $user->setRoles($rolesUser);


        $admin = new User();

        $admin->setEmail('admin@mail.com');
        $hashAdmin = $this->container->get('security.password_encoder')->encodePassword($user,'admin');
        $admin->setPassword($hashAdmin);
        $rolesUser = array();
        array_push($rolesUser, 'ROLE_ADMIN');
        $admin->setRoles($rolesUser);

        $manager->persist($user);
        $manager->persist($admin);


        $manager->flush();
    }
}
