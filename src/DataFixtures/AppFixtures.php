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
        
        // Todo : 
        // créer quelques users avec des noms prévus à l'avance pour pouvoir tester nous même les fonctionnalités
        // créer un faux utilisateur avec l'id 1 sans privilège (si attaque de bot, il prendra probablement le 1er user qui est généralement admin)
        $this->loadAdmin();
        $this->loadUser(60, "ROLE_STUDENT");
        $this->loadUser(5, "ROLE_TEACHER");
        $this->loadUser(15, "ROLE_CLIENT");
        
        $this->loadProject(15);
        
        $this->loadSchoolYear(5);
        
        $this->loadUserSchoolYearRelation(5);
        
        // Todo : 
        // ajouter relations entre student et projet
        //                   entre client et projet
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

    public function loadAdmin(): void 
    {
        $user = new User();
        $password = $this->encoder->encodePassword($user, 'admin');
        $user->setFirstname("admin")
            ->setLastname('admin')
            ->setEmail('admin@gmail.com')
            ->setPhone('0648449142')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword($password);

        $this->manager->persist($user);

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
        
        // on filtre les users en gardant que les users qui ont pour role 'ROLE_STUDENT'
        // Avec la methode array_filter
        // $users = $userRepository->findAll();
        // $students = array_filter($users, function($user) {
        //     return in_array('ROLE_STUDENT', $user->getRoles());
        // });
            
        // Avec la methode crée dans UserRepository
        $students = $userRepository->findByRole('ROLE_STUDENT');
    
        foreach ($students as $i => $student) {
            // permet de donner une schoolyear differente par cycle de $countSchoolYear
            $remainder = $i % $countSchoolYear;
            $student->setSchoolYear($schoolYears[$remainder]);
            
            $this->manager->persist($student);
        }

        $this->manager->flush();
    }
}

