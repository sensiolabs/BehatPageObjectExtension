<?php

namespace SensioLabs\PageObjectExtension\Context;

use SensioLabs\PageObjectExtension\PageObject\Page;

interface PageFactoryInterface
{
    /**
     * @param string $page
     *
     * @return Page|Element
     */
    public function create($page);
}
