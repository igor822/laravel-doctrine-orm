<?php


namespace LaravelDoctrine\ORM\Console\Generators;

class GenerateFailedJobsCommand extends GenerateMappingsCommand
{
    public function __construct()
    {
        parent::__construct('failedjob');
    }

    /**
     * Gets the names of the folders where templates for this command are stored (relative to the Mappings folder)
     *
     * @return string[]
     */
    protected function getViewNamespaces()
    {
        return ['failedjob'];
    }

    /**
     * Return generated mappings files in the structure [$mappingfileName => $mappingfileContents]
     *
     * @return array
     */
    protected function generateMappings()
    {
        $results = $this->viewFactory->make('failedjob.mappingTemplate', [
            'namespace' => $this->getNamespace()
        ])->render();

        return ['FailedJob' => $results];
    }

    /**
     * Return generated entity files in the structure [$entityfileName => $entityfileContents]
     *
     * @return array
     */
    protected function generateEntities()
    {
        //TODO write another template for annotations driver

        $results = $this->viewFactory->make('failedjob.entityTemplate', [
            'namespace' => $this->getNamespace()
        ])->render();

        return ['FailedJob' => $results];
    }
}
