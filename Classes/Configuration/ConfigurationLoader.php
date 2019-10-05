<?php

namespace Vendor\Example\Configuration;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Serializer\Normalizer;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Configuration\Loader\YamlFileLoader;
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Service\DependencyOrderingService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConfigurationLoader
{
    public static function getConfiguration()
    {
        $packageManager = GeneralUtility::makeInstance(
            PackageManager::class,
            GeneralUtility::makeInstance(DependencyOrderingService::class)
        );
        $cache = GeneralUtility::makeInstance(CacheManager::class)->getCache('example');

        $yamlFileLoader = new YamlFileLoader();
        $packages = $packageManager->getActivePackages();
        $configuration = [];

        if ($cache->has('configuration')) {
            return $cache->require('configuration');
        }

        foreach ($packages as $package) {
            $packageConfiguration = $package->getPackagePath() . 'Configuration/GraphQL.yaml';

            if (file_exists($packageConfiguration)) {
                $configuration[] = $yamlFileLoader->load($packageConfiguration);
            }
        }

        $configuration = count($configuration) > 0 ? array_replace_recursive(...$configuration) : $configuration;
        $cache->set('configuration', 'return ' . var_export($configuration ?? [], true) . ';');
        return $configuration ?? [];
    }
}