<?php

namespace SensioLabs\PageObjectExtension\Context;

use SensioLabs\PageObjectExtension\PageObject\PageObject;

interface PageFactoryInterface
{
    /**
     * @param string $page
     *
     * @return PageObject
     */
    public function create($page);
}
