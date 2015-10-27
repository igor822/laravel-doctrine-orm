<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class PasswordReminderGeneratorTest extends MigratorBase
{
    public function test_password_reminder_generator()
    {
        $generator        = new \LaravelDoctrine\ORM\Console\Generators\GeneratePasswordReminderCommand();
        $application      = new Application();
        $application->add($generator);

        $command = $application->find('doctrine:generate:mappings:passwordreminder');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'               => $command->getName(),
            'driver'                => 'yaml',
            '--entity-dest-path'    => realpath(__DIR__ . '/../../Stubs/storage') . DIRECTORY_SEPARATOR . 'generator/entity',
            '--mapping-dest-path'   => realpath(__DIR__ . '/../../Stubs/storage') . DIRECTORY_SEPARATOR . 'generator/mapping'
        ]);

        //$this->sanityCheck();
    }
}
