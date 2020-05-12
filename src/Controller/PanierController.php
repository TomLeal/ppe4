<?php


namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneReservation;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controlleur qui gère le panier.
 *
 * @Route("/panier")
 */
class PanierController extends AbstractController
{
    /**
     * Affiche le panier.
     *
     * @Route("/", name="panier", methods={"GET"})
     */
    public function index(SessionInterface $session, ProduitRepository $produitRep): Response
    {
        $panier = $session->get('panier', []);
        $panierInfo = [];
        $prixTotal = 0;
        $laCommande = $session->get('commande');

        //Si idCommande n'est pas initialisé.
        if ($laCommande==null){
            $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['courriel' => $this->getUser()->getUsername()]);

            $commande = $this->getDoctrine()->getRepository(Commande::class)->findOneBy([
                'idUtilisateur' => $utilisateur->getId(),
                'dateCde' => null
            ]);

            //Si la commande n'existe pas dans la BDD, on l'a crée et on rajoute l'id dans la session.
            if ($commande==null){
                $commande = new Commande();
                $commande->setIdUtilisateur($utilisateur);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($commande);
                $entityManager->flush();

                $session->set('commande', $commande);
            }else{
                $session->set('commande', $commande);
            }
        }

        foreach ($panier as $id => $quantite){
            $panierInfo[] = [
                'produit' => $produitRep->find($id),
                'quantite' => $quantite
            ];
        }

        foreach ($panierInfo as $p){
            $totalProduit = $p['produit']->getPrixht() * $p['quantite'];
            $prixTotal+=$totalProduit;
        }

        return $this->render('panier/index.html.twig', [
            'produits' => $panierInfo,
            'prixTotal' => $prixTotal
        ]);
    }

    /**
     * Ajout d'un produit dans le panier.
     *
     * @Route("/ajouter/{id}", name="panier_ajout", methods={"GET"})
     */
    public function ajoutProduit(Request $request, SessionInterface $session, Produit $produit)
    {
        $panier = $session->get('panier', []);
        $laCommande = $session->get('commande');

        //Si idCommande n'est pas initialisé.
        if ($laCommande==null){
            $utilisateur = $this->getDoctrine()->getRepository(Utilisateur::class)->findOneBy(['courriel' => $this->getUser()->getUsername()]);

            $commande = $this->getDoctrine()->getRepository(Commande::class)->findOneBy([
                'idUtilisateur' => $utilisateur->getId(),
                'dateCde' => null
            ]);

            //Si la commande n'existe pas dans la BDD, on l'a crée et on rajoute l'id dans la session.
            if ($commande==null){
                $commande = new Commande();
                $commande->setIdUtilisateur($utilisateur);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($commande);
                $entityManager->flush();

                $session->set('commande', $commande);
                $laCommande = $session->get('commande');
            }else{
                $session->set('commande', $commande);
                $laCommande = $session->get('commande');
            }
        }

        if (!empty($panier[$produit->getId()])){
            $panier[$produit->getId()]++;

            $lr = $this->getDoctrine()->getRepository(LigneReservation::class)->findOneBy([
                'idProduit' => $produit->getId(),
                'idCommande' => $laCommande->getId()
            ]);
            $lr->setQuantite($lr->getQuantite() + 1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lr);
            $entityManager->flush();
        }else{
            $panier[$produit->getId()] = 1;

            $lr = new LigneReservation();
            $lr->setIdCommande($laCommande)
                ->setIdProduit($produit)
                ->setQuantite(1);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->merge($lr);
            $entityManager->flush();

        }

        $session->set('panier', $panier);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Enlève 1 à la quantité du produit dans le panier.
     *
     * @Route("/retirer/{id}", name="panier_retire", methods={"GET"})
     */
    public function retireProduit(Request $request, SessionInterface $session, Produit $produit)
    {
        $panier = $session->get('panier', []);

        if ($panier[$produit->getId()] > 1){
            $panier[$produit->getId()]--;
        }else{
            unset($panier[$produit->getId()]);
        }

        $session->set('panier', $panier);

        return $this->redirect($request->headers->get('referer'));
    }
}