<?php

namespace App\Controller\API;

use App\Entity\Magasin;
use App\Entity\Produit;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class APIController extends AbstractController
{

    /**
     * Crée un fichier JSON avec tous les magasins pour les affichés sur la map de l'application mobile.
     * @Route("/magasin", name="api_magasin", methods={"GET"})
     */
    public function apiMagasin(){
        $magasins = $this->getDoctrine()
            ->getRepository(Magasin::class)
            ->findAll();
        $contenu = [];
        foreach ($magasins as $magasin){
            $contenu[] = [
                "id" => $magasin->getId(),
                "nom" => $magasin->getNom(),
                "longitude" => $magasin->getLongitude(),
                "latitude" => $magasin->getLatitude()
            ];
        }
        $contenu = '{"magasins":'.json_encode($contenu).'}';

        return new Response($contenu, 200, array(
            'Content-Type' => 'application/json'
        ));
    }

    /**
     * Crée un fichier JSON avec tous les produits pour les affichés sur la page "nos produits" de l'application mobile.
     * @Route("/produit", name="api_produit", methods={"GET"})
     */
    public function apiProduit(){
        $produits = $this->getDoctrine()
            ->getRepository(Produit::class)
            ->findAll();
        $contenu = [];
        foreach ($produits as $produit){
            $contenu[] = [
                "id" => $produit->getId(),
                "libelle" => $produit->getLibelle(),
                "description" => $produit->getDescription(),
                "prixht" => $produit->getPrixht(),
                "stock" => $produit->getStock()
            ];
        }
        $contenu = '{"produits":'.json_encode($contenu).'}';

        return new Response($contenu, 200, array(
            'Content-Type' => 'application/json'
        ));
    }

}