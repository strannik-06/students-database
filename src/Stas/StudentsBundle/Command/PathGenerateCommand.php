<?php
namespace Stas\StudentsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stas\StudentsBundle\Service\StudentService;

/**
 * Command to send Email Alerts
 */
class PathGenerateCommand extends Command
{
    const COMMAND_STRING = 'student:generate_path';

    const BATCH_SIZE = 2000;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var StudentService
     */
    protected $studentService;

    /**
     * @var array $paths
     */
    protected $paths = [];

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_STRING)
            ->setDescription('Command to generate paths for Students');
    }

    /**
     * Command execution
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $startTime = microtime(true);

        $this->manager->getConnection()->getConfiguration()->setSQLLogger(null);

        try {
            $offset = 0;
            $processedItemsCount = 0;

            while ($items = $this->studentService->getStudents(self::BATCH_SIZE, $offset)) {
                $offset += self::BATCH_SIZE;
                $countItems = count($items);
                try {
                    $success = 0;
                    foreach ($items as $item) {
                        $path = $this->studentService->generatePath($item->getName(), $this->paths);
                        $item->setPath($path);
                        $this->paths[$path] = true;
                        $success++;
                    }
                } catch (\Exception $e) {
                    $this->writeStat($output, 'Error: ' . $countItems . ' items was not processed');
                }
                $processedItemsCount += $countItems;
                $this->manager->flush();
                $this->manager->clear();
                gc_collect_cycles();
            }

            $this->writeStat($output, 'Totally processed: ' . $processedItemsCount . ' items');
        } catch (\Exception $e) {
            $this->writeStat($output, 'An error occurred: ' . $e->getMessage());
        }

        $this->writeStat($output, 'Command time: ' . round(microtime(true) - $startTime, 3) . 's');
        $this->writeStat($output, 'Memory usage: ' . round(memory_get_usage(true) / 1000000, 3) . 'Mb');
    }

    /**
     * @param OutputInterface $output
     * @param string          $message
     */
    protected function writeStat(OutputInterface $output, $message)
    {
        $output->writeln($message);
    }

    /**
     * @param EntityManager $manager
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param StudentService $studentService
     */
    public function setStudentService($studentService)
    {
        $this->studentService = $studentService;
    }
}
