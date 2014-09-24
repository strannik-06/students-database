<?php
namespace Stas\StudentsBundle\Service;

use Doctrine\ORM\EntityManager;
use Stas\StudentsBundle\Entity\Student;
use Stas\StudentsBundle\Entity\StudentRepository;

/**
 * Service for Student entity
 */
class StudentService
{
    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->manager = $entityManager;
    }

    /**
     * @param integer $limit
     * @param integer $offset
     *
     * @return Student[]
     */
    public function getStudents($limit, $offset)
    {
        /** @var StudentRepository $repository */
        $repository = $this->manager->getRepository('Stas\StudentsBundle\Entity\Student');

        return $repository->getStudents($limit, $offset);
    }

    /**
     * @param string $name
     * @param array  $existingPaths
     *
     * @return string
     */
    public function generatePath($name, $existingPaths = array())
    {
        $path = $this->encode($name);
        $i = 1;
        $newPath = $path;
        while (isset($existingPaths[$newPath])) {
            $newPath = $path . '_' . $i++;
        }

        return $newPath;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function encode($string)
    {
        $string = preg_replace('/[\d\s\W]+/', '_', $string);
        $string = mb_strtolower($string);
        $string = trim($string, '_');

        return $string;
    }
}
