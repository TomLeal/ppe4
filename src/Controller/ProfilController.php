<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ProfilType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profil")
 */
class ProfilController extends AbstractController
{
    /**
     * @Route("/", name="profli_index", methods={"GET"})
     */
    public function index(Request $request, AuthorizationCheckerInterface $auth): Response
    {
        return $this->render('profil/index.html.twig');
    }

    /**
     * @Route("/modifier/information", name="profli_modif_info", methods={"GET","POST"})
     */
    public function modifInfo(Request $request, AuthorizationCheckerInterface $auth): Response
    {
        $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['courriel' => $this->getUser()->getUsername()]);

        $form = $this->createForm(ProfilType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('profli_index');
        }

        return $this->render('profil/modif_info.html.twig', [
            'form' => $form->createView()
        ]);
    }
}