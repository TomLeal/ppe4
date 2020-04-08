<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class PageController extends AbstractController
{

    /**
     * Page d'accueil du site
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(Request $request): Response
    {
        return $this->render('page/index.html.twig', []);
    }

    /**
     * Page d'accueil du backoffice
     * @Route("/backoffice", name="index_backoffice")
     */
    public function indexBackoffice(Request $request): Response
    {
        return $this->render('page/index_backoffice.html.twig');
    }
}