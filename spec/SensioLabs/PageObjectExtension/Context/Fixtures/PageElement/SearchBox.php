<?php

use Behat\Mink\Session;

use SensioLabs\PageObjectExtension\PageObject\Element;

class SearchBox extends Element
{
    protected function xpath()
    {
        return '//div[@id="search"]';
    }
}
