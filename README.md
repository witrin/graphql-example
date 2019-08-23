# GraphQL over HTTP

This extension shows how the [GraphQL integration](https://github.com/typo3-initiatives/graphql) of the [TYPO3 Datahandler & Persistence Initiative](https://typo3.org/community/teams/typo3-development/initiatives/persistence/) could be used as a basic [HTTP endpoint](https://graphql.org/learn/serving-over-http/) without any security features.

*This implementation is a proof-of-concept prototype and thus experimental development. This extension should not be used for production sites.*

## Installation

Install the [latest development of TYPO3 CMS](https://packagist.org/packages/typo3/cms-base-distribution#dev-master) using [Composer](https://getcomposer.org/), add the dependend repositories and add this package as a dependency:

```bash
composer create-project typo3/cms-base-distribution:dev-master .
composer config minimum-stability dev
composer config repositories.cms-configuration git https://github.com/typo3-initiatives/configuration
composer config repositories.cms-security git https://github.com/typo3-initiatives/security
composer config repositories.cms-graphql git https://github.com/typo3-initiatives/graphql
composer config repositories.graphql-example git https://github.com/witrin/graphql-example
composer require example/graphql:dev-master
```

## Usage

Use POST your GraphQL queries to `/api`. The content type must be `application/json` with the following body:

```
{
    "variables": {
        ...
    },
    "query": "..."
}
```

For more detailed information about the integration of GraphQL checkout the [draft](https://docs.google.com/document/d/1M-V9H9W_tmWZI-Be9Zo5xTZUMgwJk2dMUxOFw-waO04/) and its [latest development](https://github.com/typo3-initiatives/graphql).
