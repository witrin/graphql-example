<?php

namespace Vendor\Example\Data;

use Psr\Container\ContainerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\GraphQL\Event\AfterValueResolvingEvent;
use TYPO3\CMS\GraphQL\EntitySchemaFactory;
use Vendor\Example\Configuration\ConfigurationLoader;

class ResultHandler
{
    protected $container;

    protected $processors = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(AfterValueResolvingEvent $event): void
    {
        $configuration = ConfigurationLoader::getConfiguration();
        $types = $event->getInfo()->lookAhead()->getReferencedTypes();
        $context = $event->getContext();

        $profile = $context['context']->hasAspect('profile') 
            ? $context['context']->getAspect('profile')->get('name') : 'default';

        $profiles = array_map(function ($type) use ($configuration, $profile) {
            return $configuration['profiles'][$profile][$type] 
                ?? $configuration['profiles']['default'][$type]
                ?? null;
        }, $types);

        $value = $event->getValue();

        foreach ($value as $i => $row) {
            $type = count($types) === 0 ? 0 : array_search($row[EntitySchemaFactory::ENTITY_TYPE_FIELD], $types);

            foreach ($profiles[$type] ?? [] as $field => $processors) {
                if (isset($row[$field])) {
                    foreach ($processors as $processor) {
                        $processed = $this->processors[$processor]->process($field, $row);
                        $value[$i][$field] = $processed;
                    }
                }
            }
        }

        $event->setValue($value);
    }

    public function addProcessor(ProcessorInterface $processor, string $identifier)
    {
        $this->processors[$identifier] = $processor;
    }
}