<?php

namespace spec\SensioLabs\PageObjectExtension\Context\Fixtures\Element;

use SensioLabs\PageObjectExtension\PageObject\Element;

class NamespacedSearchBox extends Element
{
    protected function getSelector()
    {
        return array('xpath' => '//div[@id="search"]');
    }
}
