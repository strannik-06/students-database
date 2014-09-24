<?php
namespace Stas\StudentsBundle\Tests\Service;

use Doctrine\ORM\EntityManager;
use Stas\StudentsBundle\Service\StudentService;
use Stas\StudentsBundle\Entity\StudentRepository;

/**
 * Test for StudentService
 *
 * @group unit
 */
class StudentServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var StudentService */
    protected $service;

    /** @var \PHPUnit_Framework_MockObject_MockObject | EntityManager */
    public $entityManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject | StudentRepository */
    protected $repositoryMock;

    /**
     * Set up
     */
    public function setUp()
    {
        $this->initEntityManagerMock(array('getRepository'));

        $this->service = new StudentService($this->entityManagerMock);

        $this->repositoryMock = $this->getMockBuilder('Stas\StudentsBundle\Entity\StudentRepository')
            ->disableOriginalConstructor()
            ->setMethods(array('getStudents'))
            ->getMock();
    }

    /**
     * Tear down
     */
    public function tearDown()
    {
        unset($this->service);
        unset($this->entityManagerMock);
    }

    /**
     * Create Doctrine Entity Manager Mock
     *
     * @param array $methods
     */
    public function initEntityManagerMock(array $methods)
    {
        /** @var \PHPUnit_Framework_TestCase $defiant */
        $defiant = $this;
        $this->entityManagerMock = $defiant->getMockBuilder('\Doctrine\ORM\EntityManager')->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test for StudentService::getStudents
     */
    public function testGetStudents()
    {
        $limit = 30;
        $offset = 60;
        $expectedResult = array(
            $this->getMockBuilder('Stas\StudentsBundle\Entity\Student')->getMock(),
        );

        $this->entityManagerMock
            ->expects($this->once())
            ->method('getRepository')
            ->with('Stas\StudentsBundle\Entity\Student')
            ->will($this->returnValue($this->repositoryMock));
        $this->repositoryMock
            ->expects($this->once())
            ->method('getStudents')
            ->with($limit, $offset)
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($expectedResult, $this->service->getStudents($limit, $offset));
    }

    /**
     * Test for StudentService::generatePath
     *
     * @param string $actual
     * @param string $expected
     * @param array  $existingPaths
     *
     * @dataProvider generatePathDataProvider
     */
    public function testGeneratePath($actual, $expected, $existingPaths)
    {
        $this->assertEquals($expected, $this->service->generatePath($actual, $existingPaths));
    }

    /**
     * Data provider for testGeneratePath
     *
     * @return array
     */
    public function generatePathDataProvider()
    {
        return array(
            'not exists' => array(
                'actual' => "John Smith",
                'expected' => 'john_smith',
                'existingPaths' => array(),
            ),
            'already exists' => array(
                'actual' => "James Black",
                'expected' => 'james_black_1',
                'existingPaths' => array(
                    'john_smith' => true,
                    'james_black' => true,
                ),
            ),
            'exists several' => array(
                'actual' => "Peter Small",
                'expected' => 'peter_small_2',
                'existingPaths' => array(
                    'peter_small' => true,
                    'peter_small_1' => true,
                ),
            ),
        );
    }

    /**
     * Test for StudentService::encode
     *
     * @param string $actual
     * @param string $expected
     *
     * @dataProvider encodeDataProvider
     */
    public function testEncode($actual, $expected)
    {
        $this->assertEquals($expected, $this->service->encode($actual));
    }

    /**
     * Data provider for testEncode
     *
     * @return array
     */
    public function encodeDataProvider()
    {
        return array(
            'big letters and numbers' => array(
                'actual' => "1John SMITH 3th",
                'expected' => 'john_smith_th',
            ),
            'bad symbols' => array(
                'actual' => "John @hello O'Hara:#",
                'expected' => 'john_hello_o_hara',
            ),
        );
    }
}
