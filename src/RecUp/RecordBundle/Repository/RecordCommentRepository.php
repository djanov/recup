<?php

namespace RecUp\RecordBundle\Repository;

use Doctrine\ORM\EntityRepository;
use RecUp\RecordBundle\Entity\Record;


class RecordCommentRepository extends EntityRepository
{
    /**
     * @param Record $record
     * @return RecordComment[]
     */
    public function findAllRecentCommentsForRecord(Record $record)
    {
        return $this->createQueryBuilder('record_comment')
            ->andWhere('record_comment.record = :record')
            ->setParameter('record', $record)
            ->andWhere('record_comment.createdAt > :recentDate')
            ->setParameter('recentDate', new \DateTime('-3 months'))
//            ->orderBy('record_comment.createdAt', 'DESC')
            ->getQuery()
            ->execute();
    }
}