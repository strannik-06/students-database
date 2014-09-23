<?php

namespace Stas\StudentsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Bundle\FrameworkBundle\Console\Application as FrameworkApplication;
use Symfony\Component\Console\Application;

class StasStudentsBundle extends Bundle
{
    /**
     * Finds and registers Commands.
     *
     * @param Application $application
     */
    public function registerCommands(Application $application)
    {
        parent::registerCommands($application);

        /** @var $application FrameworkApplication */
        $container = $application->getKernel()->getContainer();
        $application->add($container->get('stas_students.command.path_generate'));
    }
}
