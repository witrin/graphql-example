<?php

namespace Vendor\Example\Controller;

use TYPO3\CMS\Core\GraphQL\EntityReader;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\JsonView;

class GqlController extends ActionController
{
    protected $defaultViewObjectName = JsonView::class;

    public function queryAction(string $query)
    {
        $entityReader = GeneralUtility::makeInstance(EntityReader::class);

        $result = $entityReader->execute($query);
        $keys = array_keys($result);

        $this->view->setVariablesToRender($keys);
        $this->view->assign(array_pop($keys), $result);
    }
}