<?php

namespace Vendor\Example\Data;

interface ProcessorInterface
{
    public function process($field, $entity);
}