<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

use PhpSpec\ObjectBehavior;

abstract class SelectorBehavior extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith($this->getExpectedPath());
    }

    function it_returns_css_type()
    {
        $this->getType()->shouldBe($this->getExpectedType());
    }

    function it_returns_path()
    {
        $this->getPath()->shouldBe($this->getExpectedPath());
    }

    function it_returns_array_selector()
    {
        $this->asArray()->shouldBe(array($this->getExpectedType() => $this->getExpectedPath()));
    }

    abstract function getExpectedPath();

    abstract function getExpectedType();
}
