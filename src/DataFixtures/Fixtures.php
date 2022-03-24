<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use function Sodium\add;
use \DateTime;

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
        $date_expire = '2014-08-06 00:00:00';
        $date = new DateTime($date_expire);
        $now = new DateTime();

        $user = new User();

        $user->setEmail('user@mail.com');
        $hashUser = $this->container->get('security.password_encoder')->encodePassword($user,'user');
        $user->setPassword($hashUser);
        $rolesUser = array();
        array_push($rolesUser, 'ROLE_USER');
        $user->setRoles($rolesUser);
        $user->setInfo('');
        $user->setImage('');

        $admin = new User();

        $admin->setEmail('admin@mail.com');
        $hashAdmin = $this->container->get('security.password_encoder')->encodePassword($user,'admin');
        $admin->setPassword($hashAdmin);
        $rolesUser = array();
        array_push($rolesUser, 'ROLE_ADMIN');
        $admin->setRoles($rolesUser);
        $admin->setInfo('');
        $admin->setImage('');

        $manager->persist($user);
        $manager->persist($admin);

        $product1 = new Product();
        $product1->setName("Продукт 1");
        $product1->setDescription("Описание продукта 1");
        $product1->setAddedDate($date);

        $manager->persist($product1);

        $product2 = new Product();
        $product2->setName("Продукт 2");
        $product2->setDescription("Описание продукта 2");
        $product2->setAddedDate($now);

        $manager->persist($product2);

        $comment1 = new Comment();
        $comment1->setAddedDate($date);
        $comment1->setCreator($user);
        $comment1->setRelatedToProduct($product1);
        $comment1->setText("Текст 1");

        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2->setAddedDate($now);
        $comment2->setCreator($user);
        $comment2->setRelatedToProduct($product2);
        $comment2->setText("Текст 2");

        $manager->persist($comment2);

        $manager->flush();
    }
}
