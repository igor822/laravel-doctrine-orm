<?php


namespace LaravelDoctrine\ORM\Console\Generators;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePasswordReminderCommand extends GenerateMappingsCommand
{
    public function __construct()
    {
        parent::__construct('passwordreminder');
    }

    protected function configure()
    {
        $this->setName($this->getName())
            ->setAliases([$this->getName()])
            ->setDescription('Generate mapping files for ' . $this->generatorName)
            ->setDefinition([
                new InputArgument('driver', InputArgument::REQUIRED,
                    'Drive type to output mappings for'),
                new InputOption('entity-dest-path', null, InputOption::VALUE_REQUIRED,
                    'Where the generated entity should be placed'),
                new InputOption('mapping-dest-path', null, InputOption::VALUE_OPTIONAL,
                    'Where the generated mapping should be placed. Required if using yaml or xml driver.'),
                new InputOption('namespace', null, InputOption::VALUE_OPTIONAL,
                    'The namespace the entity should belong to'),
                new InputOption('useSimplified', null, InputOption::VALUE_OPTIONAL,
                    'If using yaml or xml driver, whether to use simplified names or not', false)
            ]);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (($this->namespace = $input->getOption('namespace')) === null) {
            //use out-of-box entity
            $input->setOption('namespace', 'LaravelDoctrine\ORM\Auth\Passwords');
        }
        parent::execute($input, $output);
    }

    /**
     * Gets the names of the folders where templates for this command are stored (relative to the Mappings folder)
     *
     * @return string[]
     */
    protected function getViewNamespaces()
    {
        return ['passwordreminder'];
    }

    /**
     * Return generated mappings files in the structure [$mappingfileName => $mappingfileContents]
     *
     * @return array
     */
    protected function generateMappings()
    {
        $results = $this->viewFactory->make('passwordreminder.mappingTemplate', [
            'namespace' => $this->getNamespace()
        ])->render();

        return ['PasswordReminder' => $results];
    }

    /**
     * Return generated entity files in the structure [$entityfileName => $entityfileContents]
     *
     * @return array
     */
    protected function generateEntities()
    {
        return [];
    }
}
