<?php

return [
    'frontend' => [
        'example/graphql' => [
            'target' => \Vendor\Example\Middleware\ApiEndpoint::class,
            'after' => [
                'typo3/cms-frontend/site',
                'typo3/cms-frontend/authentication',
                'typo3/cms-frontend/backend-user-authentication',
            ],
            'before' => [
                'typo3/cms-frontend/page-resolver',
            ]
        ],
    ],
];
