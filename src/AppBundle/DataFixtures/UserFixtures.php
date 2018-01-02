<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02.12.17
 * Time: 18:07
 */

namespace AppBundle\DataFixtures;

use AppBundle\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{

    public function load(ObjectManager $manager)
    {
            $user = new User();
            $user->setUsername('Username');
            $user->setEmail('username@gmail.com');
            $user->setPlainPassword('password');
            $user->setEnabled(User::ENABLED);
            $manager->persist($user);
            $manager->flush();
    }
}