<?php


namespace App\DataFixtures;

use App\Entity\Utilisateur;
use App\Service\UtilisateurService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;


class UtilisateurFixtures extends Fixture
{
    protected $candidacyService;

    public function __construct(UtilisateurService $candidacyService)
    {
        $this->candidacyService = $candidacyService;
    }

    public function load(ObjectManager $entityManager)
    {

        $user = new Utilisateur();
        $user->setCourriel('user@gmail.com');
        // On encode le mot de passe "j_ai_la_banane" dans l'utilisateur
        $user->setPlainPassword("123");
        $this->candidacyService->save($user);

        $user = new Utilisateur();
        $user->setCourriel('admin@gmail.com');
        $user->setPlainPassword("1234567");
        $user->setRoles(["ROLE_ADMIN"]);
        $this->candidacyService->save($user);
    }
}