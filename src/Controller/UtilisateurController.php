<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Image;
use App\Entity\Role;
use App\Entity\Utilisateur;
use App\Form\ConnectionType;
use App\Form\InscriptionType;
use App\Form\UtilisateurType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/backoffice/utilisateur")
 */
class UtilisateurController extends AbstractController
{
    /**
     * @Route("/", name="utilisateur_index", methods={"GET"})
     */
    public function index(): Response
    {
        $utilisateurs = $this->getDoctrine()
            ->getRepository(Utilisateur::class)
            ->findAll();

        return $this->render('utilisateur/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }

    /**
     * Page inscription
     * @Route("/inscription", name="inscription", methods={"GET","POST"})
     */
    public function inscription(Request $request): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(InscriptionType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $role = $this->getDoctrine()
                ->getRepository(Role::class)
                ->find(1);
            $image = $this->getDoctrine()
                ->getRepository(Image::class)
                ->find(1);

            $utilisateur->setMotDePasse(md5($utilisateur->getMotDePasse()));
            $utilisateur->setIdRole($role);
            $utilisateur->setIdImage($image);
            if (md5($_POST['mdpConfirme']) == $utilisateur->getMotDePasse()){
                $entittManager = $this->getDoctrine()->getManager();
                $entittManager->persist($utilisateur);
                $entittManager->flush();
            }
        }

        return $this->render('utilisateur/inscription.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView()
        ]);
    }

    /**
     * Page connexion
     * @Route("/connexion", name="connexion", methods={"GET","POST"})
     */
    public function connexion(Request $request): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(ConnectionType::class, $utilisateur);
        $form->handleRequest($request);

        return $this->render('utilisateur/connexion.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/new", name="utilisateur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($utilisateur);
            $entityManager->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/new.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="utilisateur_show", methods={"GET"})
     */
    public function show(Utilisateur $utilisateur): Response
    {
        return $this->render('utilisateur/show.html.twig', [
            'utilisateur' => $utilisateur,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="utilisateur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Utilisateur $utilisateur): Response
    {
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_index');
        }

        return $this->render('utilisateur/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="utilisateur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Utilisateur $utilisateur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($utilisateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('utilisateur_index');
    }

    public function profil(Request $request): Response
    {

    }
}
