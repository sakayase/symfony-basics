<?php

namespace App\Controller;

use App\Service\Random;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(Random $rnd, SessionInterface $session): Response
    {
        
        // affectation d'une valeur si la clé n'existe pas dans la variable d'environnement
        if (!$session->has('foo')) {
            $session->set('foo', 123);
        }

        // récupération de la valeur associée à la clé
        $foo = $session->get('foo');


        //exemple d'utilisation d'un service
        $number = $rnd->getInt();
        $projects = $rnd->getProjects();
        $meteoUrl = $rnd->getMeteoUrl();


        // Afficher le template home/index.html
        return $this->render('home/index.html.twig');

        // Redirection interne
        // return $this->redirectToRoute('project_new');

        // Redirection externe
        //return $this->redirect('http://symfony.com/doc');
    }

    /**
     * @Route("/session1/{name}")
     */
    public function session1(SessionInterface $session, string $name)
    {
        $session->set('name', $name);
        dump($name);
        exit();
    }

    /**
     * @Route("/session2")
     */
    public function session2(SessionInterface $session)
    {
        $name = $session->get('name');
        dump($name);
        exit();
    }
}
