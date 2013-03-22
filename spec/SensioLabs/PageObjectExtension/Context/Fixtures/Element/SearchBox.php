<?php

use Behat\Mink\Session;

use SensioLabs\PageObjectExtension\PageObject\Element;

class SearchBox extends Element
{
    protected function getSelector()
    {
        return array('xpath' => '//div[@id="search"]');
    }
}
