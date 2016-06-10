<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 5/22/2016
 * Time: 11:18 PM
 */

namespace RecUp\UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use FOS\UserBundle\Model\User;
use RecUp\UserBundle\Entity\UserProfile;
use Symfony\Component\Security\Core\User\UserInterface;

class UserRepository extends EntityRepository
{
    /**
     * @return UserProfile[]
     */
    public function fetchUserProfileByUser($username) // add UserInterface $user ?!
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.username = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->execute();
    }

    public function findByLetters($string)
    {
        return $this->getEntityManager()->createQuery('SELECT u 
                                                        FROM UserBundle:UserProfile u 
                                                        WHERE u.name LIKE :string 
                                                        OR u.genre LIKE :string')
                                         ->setParameter('string','%'.$string.'%')
                                         ->getResult();
    }

//    /**
//     * @return UserProfile[]
//     */
//    public function findLatestSongsByRecentUpload()
//    {
//        $record = [];
//
//        foreach ()
//
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.songs =');
//    }
}