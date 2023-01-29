<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    const USERS_LIST = [
        array(
            'username' => "admin",
            'roles' => User::ROLE_LIST
        ),
        array(
            'username' => "user",
            'roles' => []   
        ),
    ];

    private $passwordHasher;

    public function __construct(
        UserPasswordHasherInterface $passwordHasher
    )
    {
        $this->passwordHasher = $passwordHasher;    
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS_LIST as $user) {
            $newUser = new User;
            $newUser->setEmail($user['username']);
            $newUser->setPassword($this->passwordHasher->hashPassword($newUser, $user['username']));
            
            if (!empty($user['roles'])) {
                $newUser->setRoles($user['roles']);
            }

            $manager->persist($newUser);
            $manager->flush();
        }

    }
}
