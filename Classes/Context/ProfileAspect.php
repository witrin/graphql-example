<?php

namespace Vendor\Example\Context;

use TYPO3\CMS\Core\Context\AspectInterface;
use TYPO3\CMS\Core\Context\Exception\AspectPropertyNotFoundException;

class ProfileAspect implements AspectInterface
{
    /**
     * @var string
     */
    protected $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function get(string $name)
    {
        switch ($name) {
            case 'name':
                return $this->name;
        }
        throw new AspectPropertyNotFoundException('Property "' . $name . '" not found in Aspect "' . __CLASS__ . '".', 1570146950);
    }
}