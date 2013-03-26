<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context\Fixtures\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class NamespacedSearchBox extends Element
{
    protected $selector =  array('xpath' => '//div[@id="search"]');
}
