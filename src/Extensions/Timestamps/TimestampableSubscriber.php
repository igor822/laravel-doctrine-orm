<?php

namespace LaravelDoctrine\ORM\Extensions\Timestamps;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\MappingException;
use LaravelDoctrine\ORM\Extensions\Timestamps\DateResolvers\DateResolverFactory;

class TimestampableSubscriber implements EventSubscriber
{
    /**
     * @const
     */
    const CREATED_AT = 'createdAt';

    /**
     * @const
     */
    const UPDATED_AT = 'updatedAt';

    /**
     * @var array
     */
    protected static $timestampables = [];

    /**
     * @var array
     */
    protected $supportedTypes = [
        'datetime',
        'date',
        'time',
        'carbon'
    ];

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'preUpdate',
            'prePersist',
            'loadClassMetadata'
        ];
    }

    /**
     * Maps additional metadata for the Entity
     *
     * @param LoadClassMetadataEventArgs $eventArgs
     *
     * @throws MappingException
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $meta = $eventArgs->getClassMetadata();

        static::$timestampables[$meta->name] = $this->cache($eventArgs, function ($meta) {

            $entityName = $meta->name;

            $interfaces = class_implements($entityName);

            $config = [];
            if (isset($interfaces[Timestampable::class])) {
                foreach (['createdAt', 'updatedAt'] as $field) {
                    if ($meta->hasField($field)) {
                        $mapping = $meta->getFieldMapping($field);

                        if (!in_array($mapping['type'], $this->supportedTypes)) {
                            throw new MappingException('Column type ' . $mapping['type'] . ' is not supported for ' . $field);
                        }

                        $config['fields'][$field] = $mapping['type'];
                    }
                }
            }

            return $config;
        });
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $this->setDateWhenTimestampable($args, function ($field) {
            return $field === self::UPDATED_AT;
        });
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $this->setDateWhenTimestampable($args, function ($field) {
            return $field === self::CREATED_AT || $field === self::UPDATED_AT;
        });
    }

    /**
     * @param LifecycleEventArgs $args
     * @param callable           $criteria
     *
     * @internal param $entity
     */
    protected function setDateWhenTimestampable(LifecycleEventArgs $args, callable $criteria)
    {
        if ($timestampable = $this->isTimestampable($args)) {
            $entity  = $args->getEntity();
            $resolve = new DateResolverFactory();

            foreach ($timestampable['fields'] as $field => $type) {
                if ($criteria($field)) {
                    call_user_func_array(
                        [$entity, 'set' . ucfirst($field)],
                        [$resolve($type)]
                    );
                }
            }
        }
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     * @param callable                   $callback
     *
     * @return mixed
     */
    protected function cache(LoadClassMetadataEventArgs $eventArgs, callable $callback)
    {
        $meta = $eventArgs->getClassMetadata();
        $id   = $this->getCacheKey($meta->name);

        if ($cache = $eventArgs->getObjectManager()->getMetadataFactory()->getCacheDriver()) {
            $results = $cache->fetch($id);

            if ($results) {
                return $results;
            }

            $results = $callback($meta);

            $cache->save($id, $results);

            return $results;
        }

        return $callback($meta);
    }

    /**
     * @param LifecycleEventArgs $eventArgs
     *
     * @return bool
     */
    public function isTimestampable(LifecycleEventArgs $eventArgs)
    {
        $class = get_class($eventArgs->getEntity());

        if ($cache = $eventArgs->getObjectManager()->getMetadataFactory()->getCacheDriver()) {
            $results = $cache->fetch($this->getCacheKey($class));

            if ($results) {
                return $results;
            }
        }

        return isset(static::$timestampables[$class]) ? static::$timestampables[$class] : false;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    protected function getCacheKey($name)
    {
        return $name . '\\$Timestamps_CLASSMETADATA';
    }
}
