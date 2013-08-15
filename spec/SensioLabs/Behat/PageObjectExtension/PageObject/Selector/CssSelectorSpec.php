<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

require_once __DIR__.DIRECTORY_SEPARATOR.'SelectorBehavior.php';

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CssSelectorSpec extends SelectorBehavior
{
    function getExpectedPath()
    {
        return 'span.nice';
    }

    function getExpectedType()
    {
        return 'css';
    }
}
