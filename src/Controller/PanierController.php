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

            if ($commande==null){
                //Si la commande n'existe pas dans la BDD, on l'a crée et on rajoute l'id dans la session.
                $commande = new Commande();
                $commande->setIdUtilisateur($utilisateur);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($commande);
                $entityManager->flush();

                $session->set('commande', $commande);
            }else{
                //Si la commande existe dans la BDD, on rajoute l'id dans la session. Si il y a des produits dans le panier, on les rajoutes dans la session.
                $session->set('commande', $commande);

                $lesLignes = $this->getDoctrine()->getRepository(LigneReservation::class)->findBy(['idCommande' => $commande->getId()]);
                if ($lesLignes != null){
                    foreach ($lesLignes as $ligne){
                        $panier[$ligne->getIdProduit()->getId()] = $ligne->getQuantite();
                    }
                    $session->set('panier', $panier);
                }
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

            if ($commande==null){
                //Si la commande n'existe pas dans la BDD, on l'a crée et on rajoute l'id dans la session.
                $commande = new Commande();
                $commande->setIdUtilisateur($utilisateur);

                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($commande);
                $entityManager->flush();

                $session->set('commande', $commande);
                $laCommande = $session->get('commande');
            }else{
                //Si la commande existe dans la BDD, on rajoute l'id dans la session. Si il y a des produits dans le panier, on les rajoutes dans la session.
                $session->set('commande', $commande);
                $laCommande = $session->get('commande');

                $lesLignes = $this->getDoctrine()->getRepository(LigneReservation::class)->findBy(['idCommande' => $commande->getId()]);
                if ($lesLignes != null){
                    foreach ($lesLignes as $ligne){
                        $panier[$ligne->getIdProduit()->getId()] = $ligne->getQuantite();
                    }
                    $session->set('panier', $panier);
                }
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
        $laCommande = $session->get('commande');
        $lr = $this->getDoctrine()->getRepository(LigneReservation::class)->findOneBy([
            'idProduit' => $produit->getId(),
            'idCommande' => $laCommande->getId()
        ]);

        if ($panier[$produit->getId()] > 1){
            $panier[$produit->getId()]--;
            $lr->setQuantite($lr->getQuantite() - 1);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($lr);
            $entityManager->flush();
        }else{
            unset($panier[$produit->getId()]);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($lr);
            $entityManager->flush();
        }

        $session->set('panier', $panier);

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Page de validation de la commande. On choisit l'adresse ici.
     *
     * @Route("/commande}", name="panier_commande", methods={"GET"})
     */
    public function commande(Request $request, SessionInterface $session, ProduitRepository $produitRep){
        $panier = $session->get('panier', []);
        $panierInfo = [];
        $prixTotal = 0;

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

        return $this->render('panier/commande.html.twig', [
            'produits' => $panierInfo,
            'prixTotal' => $prixTotal
        ]);
    }

    /**
     * Page de payement de la commande.
     *
     * @Route("/payement}", name="panier_payement", methods={"GET"})
     */
    public function payement(Request $request, SessionInterface $session, ProduitRepository $produitRep){
        $panier = $session->get('panier', []);
        $panierInfo = [];
        $prixTotal = 0;

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

        return $this->render('panier/payement.html.twig', [
            'produits' => $panierInfo,
            'prixTotal' => $prixTotal
        ]);
    }

    /**
     * A REMPLACER PAR PAYPAL
     * Traitement du payement.
     *
     * @Route("/payert}", name="panier_payer", methods={"GET"})
     */
    public function payer(Request $request, SessionInterface $session){
        $commande = $session->get('commande');
        $date = new \DateTime();

        $date->setDate(date("Y"), date("m"), date("d"));
        $commande->setDateCde($date);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->merge($commande);
        $entityManager->flush();

        $session->clear();
        return $this->redirectToRoute('index');
    }
}