<?php

namespace spec\SensioLabs\PageObjectExtension\Context\Fixtures\NamespacedArticleList;

use SensioLabs\PageObjectExtension\PageObject\Element;

class SearchBox extends Element
{
    protected function xpath()
    {
        return '//div[@id="search"]';
    }
}
