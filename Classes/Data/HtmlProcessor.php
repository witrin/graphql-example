<?php

namespace Vendor\Example\Data;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use Vendor\Example\Configuration\ConfigurationLoader;

class HtmlProcessor implements ProcessorInterface
{
    /**
     * @var string
     */
    protected $parseFunctionTypoScriptPath;

    public function __construct(string $parseFunctionTypoScriptPath = 'lib.parseFunc_RTE')
    {
        $this->parseFunctionTypoScriptPath = $parseFunctionTypoScriptPath;
    }

    public function process($field, $record)
    {
        $contentObject = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $contentObject->start([]);

        return $contentObject->parseFunc($record[$field], [], '< ' . $this->parseFunctionTypoScriptPath);
    }
}