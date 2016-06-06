<?php
/**
 * Created by PhpStorm.
 * User: jkr
 * Date: 4/4/2016
 * Time: 9:51 PM
 */

namespace RecUp\RecordBundle\Repository;

use Doctrine\ORM\EntityRepository;
use RecUp\RecordBundle\Entity\Record;


class SongsRepository extends EntityRepository
{
    /**
     * @return Record[]
     */
    public function findAllPublishedOrderedByRecentlyActive()
    {
        return $this->createQueryBuilder('song')
            ->andWhere('song.isPublished = :isPublished')
            ->setParameter('isPublished', true)
            ->leftJoin('song.comments', 'record_comment')
            ->orderBy('record_comment.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }
    
}