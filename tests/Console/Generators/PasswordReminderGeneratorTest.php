<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use LaravelDoctrine\ORM\Auth\Passwords\PasswordReminder;
use Symfony\Component\Yaml\Yaml;

class PasswordReminderGeneratorTest extends GeneratorBase
{
    public function test_password_reminder_generator_yaml()
    {
        $generator        = new \LaravelDoctrine\ORM\Console\Generators\GeneratePasswordReminderCommand();
        $application      = new Application();
        $application->add($generator);

        $command = $application->find('doctrine:generate:mappings:passwordreminder');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'               => $command->getName(),
            'driver'                => 'yaml',
            '--entity-dest-path'    => $this->entityDir,
            '--mapping-dest-path'   => $this->mappingDir,
        ]);

        //file was generated with correct name
        $this->assertTrue(is_file($this->mappingDir . DIRECTORY_SEPARATOR . $generator->generateMappingName('PasswordReminder')));

        //is parsable YAML file
        $mappingResult = Yaml::parse(file_get_contents($this->mappingDir . DIRECTORY_SEPARATOR . $generator->generateMappingName('PasswordReminder')));

        //root node has correct name
        $this->assertTrue(isset($mappingResult[PasswordReminder::class]));
    }

    public function test_password_reminder_generator_custom_namespace_yaml()
    {
        $generator        = new \LaravelDoctrine\ORM\Console\Generators\GeneratePasswordReminderCommand();
        $application      = new Application();
        $application->add($generator);

        $command = $application->find('doctrine:generate:mappings:passwordreminder');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'               => $command->getName(),
            'driver'                => 'yaml',
            '--entity-dest-path'    => $this->entityDir,
            '--mapping-dest-path'   => $this->mappingDir,
            '--namespace'           => '\App\MyModels\Sub'
        ]);

        //file was generated with correct name
        $this->assertTrue(is_file($this->mappingDir . DIRECTORY_SEPARATOR . $generator->generateMappingName('PasswordReminder')));

        //is parsable YAML file
        $mappingResult = Yaml::parse(file_get_contents($this->mappingDir . DIRECTORY_SEPARATOR . $generator->generateMappingName('PasswordReminder')));

        //root node has correct name
        $this->assertTrue(isset($mappingResult['App\MyModels\Sub\PasswordReminder']));
    }
}
