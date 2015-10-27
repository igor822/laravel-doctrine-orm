<?php

namespace LaravelDoctrine\ORM\Console\Generators;

use Illuminate\Contracts\View\Factory;
use InvalidArgumentException;
use LaravelDoctrine\ORM\Utilities\TemplateFactory;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class GenerateMappingsCommand extends SymfonyCommand
{
    /**
     * @var string The name of the mappings being generated
     * */
    protected $generatorName;

    /**
     * @var Factory
     */
    protected $viewFactory;

    protected $useSimplified;
    protected $namespace;
    protected $driver;

    /**
     * Gets the names of the folders where templates for this command are stored (relative to the Mappings folder)
     *
     * @return string[]
     */
    abstract protected function getViewNamespaces();

    /**
     * Return generated mappings files in the structure [$mappingfileName => $mappingfileContents]
     *
     * @return array
     */
    abstract protected function generateMappings();

    /**
     * Return generated entity files in the structure [$entityfileName => $entityfileContents]
     *
     * @return array
     */
    abstract protected function generateEntities();

    public function __construct($name)
    {
        $this->generatorName = $name;

        parent::__construct('doctrine:generate:mappings:' . $name);

        $this->viewFactory = TemplateFactory::createViewFactory(realpath(__DIR__ . '/Mappings'));

        foreach ($this->getViewNamespaces() as $namespace) {
            $this->viewFactory->addNamespace($namespace, realpath(__DIR__ . "/Mappings/$namespace"));
        }
    }

    /**
     * Configure the command
     */
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
                new InputOption('namespace', null, InputOption::VALUE_REQUIRED,
                    'The namespace the entity should belong to'),
                new InputOption('useSimplified', null, InputOption::VALUE_OPTIONAL,
                    'If using yaml or xml driver, whether to use simplified names or not', false)
            ]);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        if (($entityDestPath = $input->getOption('entity-dest-path')) === null) {
            throw new InvalidArgumentException('Missing destination path');
        }

        if (($this->driver = $input->getArgument('driver')) === null) {
            throw new InvalidArgumentException('Missing driver argument');
        }

        $mappingDestPath = $input->getOption('mapping-dest-path');

        if (in_array($this->driver, ['yaml', 'xml']) && $mappingDestPath === null) {
            throw new InvalidArgumentException('You must specify a destination path for mapping files');
        }

        if (($this->namespace = $input->getOption('namespace')) === null) {
            throw new InvalidArgumentException('Missing namespace argument');
        }

        if (($this->useSimplified = $input->getOption('useSimplified')) === null) {
            $this->useSimplified = false;
        }

        foreach ([$entityDestPath, $mappingDestPath] as $path) {
            if (!is_null($path)) {
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }

                if (!is_writable($path)) {
                    throw new InvalidArgumentException(
                        sprintf("Destination directory '<info>%s</info>' does not have write permissions.",
                            $path)
                    );
                }
            }
        }

        foreach ($this->generateEntities() as $entityName => $entityContents) {
            file_put_contents(realpath($entityDestPath) . DIRECTORY_SEPARATOR . $entityName . '.php', '<?php ' . PHP_EOL . $entityContents);
        }
        foreach ($this->generateMappings() as $mappingName => $mappingContents) {
            file_put_contents(realPath($mappingDestPath) . DIRECTORY_SEPARATOR . $this->generateMappingName($mappingName), $mappingContents);
        }

        $output->writeln('Files generated successfully.');
    }

    /**
     * Get the string that will be used as the file name for a mapping file
     *
     * @param $name
     *
     * @throws \Exception
     * @return string
     */
    public function generateMappingName($name)
    {
        if ($this->useSimplified) {
            return $name . $this->getMappingExtension();
        } else {
            //replace slashes with periods to conform to naming convention
            $convertedNamespace = str_replace('\\', '.', $this->getNamespace());

            //If the user left off a trailing slash we need to add the period for them
            if (substr($convertedNamespace, -1, 1) !== '.') {
                $convertedNamespace .= '.';
            }

            return $convertedNamespace . $name . $this->getMappingExtension();
        }
    }

    /**
     * Get the extension to be used for the mapping file
     *
     * @throws \Exception
     * @return null|string
     */
    public function getMappingExtension()
    {
        switch ($this->driver) {
            case 'yaml':
                return $this->useSimplified ? '.orm.yml' : '.dcm.yml';
            case 'xml':
                return $this->useSimplified ? '.orm.xml' : '.dcm.yml';
            case 'annotation':
                return null;
            default:
                throw new \Exception('No driver specified');
        }
    }

    /**
     * Get namespace as a string.
     *
     * Removes any leading slashes.
     *
     * @return string
     */
    protected function getNamespace()
    {
        if (substr($this->namespace, 0, 1) == '\\') {
            return substr($this->namespace, 1);
        } else {
            return $this->namespace;
        }
    }
}
