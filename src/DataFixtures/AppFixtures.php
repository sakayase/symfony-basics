<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\User;
use DateTime;
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
        $this->manager = $manager;
        // rend utilisable manager dans toute la classe
        $this->faker = \Faker\Factory::create('fr_FR');
        
        $this->loadUser(60, "ROLE_STUDENT");
        $this->loadUser(5, "ROLE_TEACHER");
        $this->loadUser(15, "ROLE_CLIENT");

        $this->loadProject(15);

        $this->loadSchoolYear(5);
        
        $this->loadUserSchoolYearRelation(5);

    }
    
    public function loadUser(int $count, string $role): void 
    {
        // créer un générateur de fausses données, localisé pour le français
        // composer require fzaninotto/faker
        
        //students
        for($i = 0; $i < $count; $i++) {
            // crée les données
            $user = new User();
    
            $firstname = $this->faker->firstName();
            $lastname = $this->faker->lastName();
            $roles = [$role];
            $password = $this->encoder->encodePassword($user, '123');
    
            $phone = $this->faker->serviceNumber();
    
            // met les données crées dans le new User
    
            // design patern fluent
            $user->setFirstname($firstname) // setFirstname() renvoie l'objet courrant,
                ->setLastname($lastname)    // donc sur l'objet on peut appliquer toutes ses fonctions par la suite
                ->setEmail(strtolower($firstname).'.'.strtolower($lastname).'-'.$i.'@example.com')
                ->setPhone($phone)
                ->setRoles($roles)
                ->setPassword($password);
    
            // stock dans la bdd
            $this->manager->persist($user); 
    
            // pour generer les fixtures : php bin/console doctrine:fixtures:load
        }
    
        $this->manager->flush();
    }

    public function loadProject(int $count): void 
    {
        for($i = 0; $i < $count; $i++) {
            
            $name = $this->faker->sentence(6, true);
            
            $description = null;
            if (random_int(0, 100) <= 33) {
                $description = $this->faker->realText(200);
            }
            
            $project = new Project();
            $project->setName($name)
                ->setDescription($description);

            $this->manager->persist($project);
        }
        $this->manager->flush();
    }

    public function loadSchoolYear(int $count): void 
    {
        $year = 2020;

        for($i = 0; $i < $count; $i++) {
            $name = $this->faker->realText(100);
            $dateStart = new DateTime();
            $dateEnd = new DateTime();

            if ($i % 2 == 0) {
                $dateStart->setDate($year, 1, 1);
                $dateEnd->setDate($year, 6, 30);
            } else {
                $dateStart->setDate($year, 7, 1);
                $dateEnd->setDate($year, 12, 31);
                $year++;
            }

            $schoolYear = new SchoolYear();
            $schoolYear->setName($name)
                ->setDateStart($dateStart)
                ->setDateEnd($dateEnd);

            $this->manager->persist($schoolYear); 
        }

        $this->manager->flush();
    }

    public function loadUserSchoolYearRelation(int $countSchoolYear): void 
    {
        //on recup le repository des promos
        $schoolYearRepository = $this->manager->getRepository(SchoolYear::class);
        //on stock les promos dans un tableau
        $schoolYears = $schoolYearRepository->findAll();

        $userRepository = $this->manager->getRepository(User::class);
        $users = $userRepository->findAll();
        // $users = $userRepository->findBy([
        //     'roles' => ["ROLE_STUDENT"]
        //     ]);
    
        foreach ($users as $i => $user) {
            // permet de donner une schoolyear differente par cycle de $countSchoolYear
            $remainder = $i % $countSchoolYear;
            $user->setSchoolYear($schoolYears[$remainder]);
            
            $this->manager->persist($user);
        }

        $this->manager->flush();
    }
}
