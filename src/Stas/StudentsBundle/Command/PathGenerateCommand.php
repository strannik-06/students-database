<?php
namespace Stas\StudentsBundle\Command;

use Symfony\Component\Console\Command\Command;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Stas\StudentsBundle\Service\StudentService;

/**
 * Command to send Email Alerts
 */
class PathGenerateCommand extends Command
{
    const COMMAND_STRING = 'student:generate_path';

    const BATCH_SIZE = 1000;

    /**
     * @var EntityManager
     */
    protected $manager;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var StudentService
     */
    protected $studentService;

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

            $this->writeStat($output, 'Totally processed: ' . $processedItemsCount . ' queries');
        } catch (\Exception $e) {
            $output->writeln('An error occurred: ' . $e->getMessage());
            $this->logger->error($e->getMessage() . "\n" . $e->getTraceAsString());
        }

        $this->writeStat($output, 'Command time: ' . round(microtime(true) - $startTime, 3));
        $this->writeStat($output, 'Memory usage: ' . round(memory_get_usage(true) / 1000000, 3));
    }

    /**
     * @param OutputInterface $output
     * @param string          $message
     */
    protected function writeStat(OutputInterface $output, $message)
    {
        $output->writeln($message);
        $this->logger->info($message);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
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
