<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element as BaseElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorInterface;

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

class ElementSpec extends ObjectBehavior
{
    function let(Session $session, PageFactoryInterface $factory, SelectorFactoryInterface $selectorFactory, SelectorsHandler $selectorsHandler, SelectorInterface $selector)
    {
        // until we have proper abstract class support in PhpSpec
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyElement');
        $this->beConstructedWith($session, $factory, $selectorFactory);

        $type = 'xpath';
        $path = '//div[@id="my-box"]';
        $selectorFactory->create(array($type => $path))->willReturn($selector);
        $selector->getPath()->willReturn($path);
        $selector->getType()->willReturn($type);
        $selector->asArray()->willReturn(array($type => $path));

        $session->getSelectorsHandler()->willReturn($selectorsHandler);
        $selectorsHandler->selectorToXpath('xpath', '//div[@id="my-box"]')->willReturn('//div[@id="my-box"]');
    }

    function it_should_be_a_node_element()
    {
        $this->shouldHaveType('Behat\Mink\Element\NodeElement');
    }

    function it_should_relate_to_a_subsection_of_a_page()
    {
        $this->getXpath()->shouldReturn('//div[@id="my-box"]');
    }

    function it_gives_clear_feedback_if_method_is_invalid()
    {
        $this->shouldThrow(new \BadMethodCallException('"search" method is not available on the MyElement'))->during('search');
    }

    function it_creates_a_page(PageFactoryInterface $factory, Page $page)
    {
        $factory->createPage('Home')->willReturn($page);

        $this->callGetPage('Home')->shouldReturn($page);
    }

    function it_returns_the_element_name()
    {
        $this->callGetName()->shouldReturn('MyElement');
    }
}
