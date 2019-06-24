# GraphQL over HTTP

This extension shows how the [GraphQL integration](https://github.com/typo3-initiatives/graphql) of the [TYPO3 Datahandler & Persistence Initiative](https://typo3.org/community/teams/typo3-development/initiatives/persistence/) could be used as a basic [HTTP endpoint](https://graphql.org/learn/serving-over-http/) without any security features.

*This implementation is a proof-of-concept prototype and thus experimental development. This extension should not be used for production sites.*

## Installation

Install the [latest development of TYPO3 CMS](https://packagist.org/packages/typo3/cms-base-distribution#dev-master) using [Composer](https://getcomposer.org/). In your project root add this repository and a dependency to this package:

```bash
composer config repositories.graphql-example git https://github.com/witrin/graphql-example
composer require example/graphql
```

Include the static template `GraphQL API` into your root template.

## Usage

Use the following URL and replace `<query>` withour your GraphQL query:

```
/?type=1561325893&tx_example_pi1[query]=<query>
```

For more detailed information about the integration of GraphQL checkout the [draft](https://docs.google.com/document/d/1M-V9H9W_tmWZI-Be9Zo5xTZUMgwJk2dMUxOFw-waO04/) and its [latest development](https://github.com/typo3-initiatives/graphql).