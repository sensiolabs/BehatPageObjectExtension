<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element as BaseElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class MyElement extends BaseElement
{
    public $selector = array('xpath' => '//div[@id="my-box"]');

    public function callGetPage($name)
    {
        return $this->getPage($name);
    }

    public function callGetName()
    {
        return $this->getName();
    }
}

class MySimpleElement extends BaseElement
{
    public $selector = 'div#my-box';
}

class ElementSpec extends ObjectBehavior
{
    function let(Session $session, Factory $factory, SelectorsHandler $selectorsHandler)
    {
        // until we have proper abstract class support in PhpSpec
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyElement');
        $this->beConstructedWith($session, $factory);

        $session->getSelectorsHandler()->willReturn($selectorsHandler);
        $selectorsHandler->selectorToXpath('xpath', '//div[@id="my-box"]')->willReturn('//div[@id="my-box"]');
    }

    function it_should_be_a_page_object()
    {
        $this->shouldHaveType('SensioLabs\Behat\PageObjectExtension\PageObject\PageObject');
    }

    function it_should_be_a_node_element()
    {
        $this->shouldHaveType('Behat\Mink\Element\NodeElement');
    }

    function it_should_relate_to_a_subsection_of_a_page()
    {
        $this->getXpath()->shouldReturn('//div[@id="my-box"]');
    }

    function it_assumes_a_css_selector_if_not_specified(Session $session, Factory $factory, SelectorsHandler $selectorsHandler)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MySimpleElement');
        $this->beConstructedWith($session, $factory);

        $selectorsHandler->selectorToXpath('css', 'div#my-box')->willReturn('//div[@id="my-box"]');

        $this->getXpath()->shouldReturn('//div[@id="my-box"]');
    }

    function it_gives_clear_feedback_if_method_is_invalid()
    {
        $this->shouldThrow(new \BadMethodCallException('"search" method is not available on the MyElement'))->during('search');
    }

    function it_creates_a_page(Factory $factory, Page $page)
    {
        $factory->createPage('Home')->willReturn($page);

        $this->callGetPage('Home')->shouldReturn($page);
    }

    function it_returns_the_element_name()
    {
        $this->callGetName()->shouldReturn('MyElement');
    }
}
