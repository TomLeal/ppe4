<?php


namespace App\Service;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UtilisateurService
{
    protected $em;
    protected $repository;
    protected $passwordEncoder;

    /**
     * UserService constructor.
     *
     * @param EntityManagerInterface $em by dependency injection
     */
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->em = $em;
        $this->repository = $this->em->getRepository(Utilisateur::class);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Set a password encoded to a user
     *
     * @param Utilisateur $user
     * @param String $passwordInCLear
     */
    public function encodePassword(Utilisateur $user)
    {
        $plainPassword = $user->getPlainPassword();

        if (!empty($plainPassword))
        {
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $plainPassword
            ));
        }

        return $user;
    }


    /**
     * Delete a user object in bdd
     *
     * @param Utilisateur $user
     */
    public function delete(Utilisateur $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }


    /**
     * Save a user object in bdd
     *
     * @param Utilisateur $user
     */
    public function save(Utilisateur $user)
    {
        $user = $this->encodePassword($user);
        $this->em->persist($user);
        $this->em->flush();
    }
}