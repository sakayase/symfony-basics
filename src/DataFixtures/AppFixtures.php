<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {   
        //students
        for($i = 0; $i < 60; $i++) {
            // crée les données
            $user = new User();
            $firstname = "Toto$i";
            $lastname = "Titi";
            $roles = ["ROLE_STUDENT"];
            $password = $this->encoder->encodePassword($user, strtolower($firstname));

            if ($i < 10) {
                $phone = "012345670$i";
            } else {
                $phone = "01234567$i";
            }

            // met les données crées dans le new User
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail(strtolower($firstname).'.'.strtolower($lastname).'@example.com');
            $user->setPhone($phone);
            $user->setRoles($roles);
            $user->setPassword($password);

            // stock dans la bdd
            $manager->persist($user); 

            // pour generer les fixtures : php bin/console doctrine:fixtures:load
        }

        $manager->flush();
    }
}
