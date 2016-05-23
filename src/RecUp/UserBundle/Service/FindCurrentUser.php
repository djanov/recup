<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 5/23/2016
 * Time: 2:20 PM
 */

namespace RecUp\UserBundle\Service;


use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FindCurrentUser
{
    private $tokenStorage;

    private $em;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $em)
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    public function getUserProfileDataByUser()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return $this->em->getRepository('UserBundle:UserProfile')->fetchUserProfileByUser($user);
    }
}