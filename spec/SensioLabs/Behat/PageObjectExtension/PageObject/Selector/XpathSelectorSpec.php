<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

require_once __DIR__.DIRECTORY_SEPARATOR.'SelectorBehavior.php';

use Prophecy\Argument;

class XpathSelectorSpec extends SelectorBehavior
{
    function getExpectedPath()
    {
        return '//div/span[@class="navigation"]';
    }

    function getExpectedType()
    {
        return 'xpath';
    }
}
