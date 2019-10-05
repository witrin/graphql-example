<?php

$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['example'] = [
    'frontend' => \TYPO3\CMS\Core\Cache\Frontend\PhpFrontend::class,
    'backend' => \TYPO3\CMS\Core\Cache\Backend\SimpleFileBackend::class,
    'options' => [
        'defaultLifetime' => 0,
    ],
    'groups' => ['system']
];