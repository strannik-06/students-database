<?php

namespace Stas\StudentsBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Stas\StudentsBundle\Entity\Student;

/**
 * Class StudentRepository
 */
class StudentRepository extends EntityRepository
{
    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return Student[]
     */
    public function getStudents($limit, $offset)
    {
        $builder = $this->createQueryBuilder('s');

        return $builder
            ->select('s')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
