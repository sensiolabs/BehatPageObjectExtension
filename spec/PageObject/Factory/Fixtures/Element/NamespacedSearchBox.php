<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Factory\Fixtures\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class NamespacedSearchBox extends Element
{
    protected $selector =  array('xpath' => '//div[@id="search"]');
}
