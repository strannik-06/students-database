<?php
namespace Stas\StudentsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Stas\StudentsBundle\Entity\StudentRepository;

/**
 * Class StudentController
 */
class StudentController extends Controller
{
    /**
     * @param string $path
     *
     * @return Response
     */
    public function detailsAction($path)
    {
        $repository = $this->getDoctrine()->getRepository('StasStudentsBundle:Student');
        /** @var StudentRepository $repository */
        $student = $repository->findOneBy(array('path' => $path));

        $response = $this->render('StasStudentsBundle:Student:details.html.twig',
            array('student' => $student));
        $response->setMaxAge(900);
        $response->setPublic();

        return $response;
    }
}
