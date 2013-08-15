<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SelectorFactorySpec extends ObjectBehavior
{
    const SELECTOR_CSS = 'SensioLabs\Behat\PageObjectExtension\PageObject\Selector\CssSelector';
    const SELECTOR_XPATH = 'SensioLabs\Behat\PageObjectExtension\PageObject\Selector\XpathSelector';

    function let()
    {
        $this->registry('css', self::SELECTOR_CSS);
        $this->registry('xpath', self::SELECTOR_XPATH);
    }

    function it_creates_new_selector_from_array_args()
    {
        $this->create(array('xpath' => '//'))->shouldBeAnInstanceOf(self::SELECTOR_XPATH);
    }

    function it_creates_new_selector_from_args()
    {
        $this->create('css', 'div.test')->shouldBeAnInstanceOf(self::SELECTOR_CSS);
    }

    function it_throws_an_exception_for_missing_selector_type()
    {
        $this->shouldThrow('\SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidSelectorDeclarationException')
            ->duringCreate('non', 'div.test');
    }

    function it_throws_an_exception_for_invalid_selector_declarations()
    {
        $invalid = array(
            array('css', 'div.test'),
            'css',
            null,
            array(''),
            array(0 => 'test')
        );

        foreach ($invalid as $declaration) {
            $this->shouldThrow('\SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidSelectorDeclarationException')
                ->duringCreate($invalid);
        }

        $invalid = array(
            array(null, null),
            array(null, 'test'),
            array(null, array()),
        );

        foreach ($invalid as $declaration) {
            $this->shouldThrow('\SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidSelectorDeclarationException')
                ->duringCreate($declaration[0], $declaration[1]);
        }
    }

    function it_throws_an_exception_for_empty_declaration()
    {
        $this->shouldThrow('\SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidSelectorDeclarationException')
            ->duringCreate();
    }

    function it_throws_an_exception_for_more_than_two_arguments_declaration()
    {
        $this->shouldThrow('\SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidSelectorDeclarationException')
            ->duringCreate('css', 'div.nice', true);
    }
}
