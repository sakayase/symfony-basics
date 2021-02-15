<?php

namespace App\Controller;

use App\Services\Random;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Random $random): Response
    {
        $number = $random->getInt();
        dump($number);
        $projects = $random->getProjects();
        dump($projects);
        exit();
        // Afficher le template home/index.html
        return $this->render('home/index.html.twig');

        // Redirection interne
        // return $this->redirectToRoute('project_new');

        // Redirection externe
        //return $this->redirect('http://symfony.com/doc');
    }
}
