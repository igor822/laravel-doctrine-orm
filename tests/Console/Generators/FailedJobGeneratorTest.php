<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Yaml\Yaml;

class FailedJobGeneratorTest extends GeneratorBase
{
    public function test_failed_job_generator_yaml()
    {
        $namespace = 'App\MyModels\Sub';

        $generator        = new \LaravelDoctrine\ORM\Console\Generators\GenerateFailedJobsCommand();
        $application      = new Application();
        $application->add($generator);

        $command = $application->find('doctrine:generate:mappings:failedjob');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'               => $command->getName(),
            'driver'                => 'yaml',
            '--entity-dest-path'    => $this->entityDir,
            '--mapping-dest-path'   => $this->mappingDir,
            '--namespace'           => $namespace
        ]);

        //mapping file was generated with correct name
        $this->assertTrue(is_file($this->mappingDir . DIRECTORY_SEPARATOR . $generator->generateMappingName('FailedJob')));

        //is a parsable YAML file
        $mappingResult = Yaml::parse(file_get_contents($this->mappingDir . DIRECTORY_SEPARATOR . $generator->generateMappingName('FailedJob')));

        //root node has correct name
        $this->assertTrue(isset($mappingResult['App\MyModels\Sub\FailedJob']));

        //entity file was generated with correct name
        $this->assertTrue(is_file($this->entityDir . DIRECTORY_SEPARATOR . 'FailedJob.php'));

        //check to make sure the file has the correct namespace
        $this->assertTrue($namespace == $this->getNamespace(file_get_contents($this->entityDir . DIRECTORY_SEPARATOR . 'FailedJob.php')));
    }
}
