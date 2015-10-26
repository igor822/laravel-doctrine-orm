<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class FailedJobGeneratorTest extends MigratorBase
{
    public function test_failed_job_generator()
    {
        $generator        = new \LaravelDoctrine\ORM\Console\Generators\GenerateFailedJobsCommand();
        $application      = new Application();
        $application->add($generator);

        $command = $application->find('doctrine:generate:mappings:failedjob');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'               => $command->getName(),
            'driver'                => 'yaml',
            '--entity-dest-path'    => realpath(__DIR__ . '/../../Stubs/storage') . DIRECTORY_SEPARATOR . 'generator/entity',
            '--mapping-dest-path'   => realpath(__DIR__ . '/../../Stubs/storage') . DIRECTORY_SEPARATOR . 'generator/mapping',
            '--namespace'           => '\App\MyModels\Sub'
        ]);

        //$this->sanityCheck();
    }
}
