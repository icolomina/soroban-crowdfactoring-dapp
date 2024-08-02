<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{

    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ){}

    public function load(ObjectManager $manager): void
    {
        
        $userCompany1 = new User();
        $userCompany1->setEmail('greencycle@gmail.com');
        $userCompany1->setName('GreenCycle Inc');
        $userCompany1->setCreatedAt(new \DateTimeImmutable());
        $userCompany1->setPassword($this->passwordHasher->hashPassword($userCompany1, 'userf1'));
        $userCompany1->setRoles([User::ROLE_FINANCIAL_ENTITY]);

        $userCompany2 = new User();
        $userCompany2->setEmail('medtech@gmail.com');
        $userCompany2->setName('MedTech Solutions Ltd');
        $userCompany2->setCreatedAt(new \DateTimeImmutable());
        $userCompany2->setPassword($this->passwordHasher->hashPassword($userCompany2, 'userf2'));
        $userCompany2->setRoles([User::ROLE_FINANCIAL_ENTITY]);

        $userSaver1 = new User();
        $userSaver1->setEmail('peter.parker@gmail.com');
        $userSaver1->setName('Peter Parker');
        $userSaver1->setCreatedAt(new \DateTimeImmutable());
        $userSaver1->setPassword($this->passwordHasher->hashPassword($userSaver1, 'users1'));
        $userSaver1->setRoles([User::ROLE_SAVER]);

        $userSaver2 = new User();
        $userSaver2->setEmail('clark.kent@gmail.com');
        $userSaver2->setName('Clark Kent');
        $userSaver2->setCreatedAt(new \DateTimeImmutable());
        $userSaver2->setPassword($this->passwordHasher->hashPassword($userSaver2, 'users2'));
        $userSaver2->setRoles([User::ROLE_SAVER]);

        $userSaver3 = new User();
        $userSaver3->setEmail('diana.prince@gmail.com');
        $userSaver3->setName('Diana Prince');
        $userSaver3->setCreatedAt(new \DateTimeImmutable());
        $userSaver3->setPassword($this->passwordHasher->hashPassword($userSaver3, 'users3'));
        $userSaver3->setRoles([User::ROLE_SAVER]);

        $manager->persist($userCompany1);
        $manager->persist($userCompany2);
        $manager->persist($userSaver1);
        $manager->persist($userSaver2);
        $manager->persist($userSaver3);

        $manager->flush();
    }
}
