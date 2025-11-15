<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; ++$i) {
            $user = new User();
            $user->setEmail(email: 'test'.$i.'@mail.ru');
            $user->setPassword(password: 'lalala'.$i);
            $user->setRoles(roles: [Role::ROLE_USER->value]);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
