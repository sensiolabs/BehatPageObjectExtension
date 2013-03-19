<?php

namespace spec\SensioLabs\PageObjectExtension\PageObject;

use PHPSpec2\ObjectBehavior;
use SensioLabs\PageObjectExtension\PageObject\PageElement as BasePageElement;

class MyPageElement extends BasePageElement
{
    protected static function xpath()
    {
        return '//div[@id="my-box"]';
    }

    public function callGetPage($name)
    {
        return $this->getPage($name);
    }

    public function callGetName()
    {
        return $this->getName();
    }
}

class PageElement extends ObjectBehavior
{
    /**
     * @param \Behat\Mink\Session                                          $session
     * @param \SensioLabs\PageObjectExtension\Context\PageFactoryInterface $factory
     */
    function let($session, $factory)
    {
        // until we have proper abstract class support in PHPSpec2
        $this->beAnInstanceOf('spec\SensioLabs\PageObjectExtension\PageObject\MyPageElement');
        $this->beConstructedWith($session, $factory);
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
        $this->shouldThrow(new \BadMethodCallException('"search" method is not available on the MyPageElement'))->during('search');
    }

    function it_creates_a_page($factory, $page)
    {
        $page->beAnInstanceOf('SensioLabs\PageObjectExtension\PageObject\PageElement');

        $factory->create('Home')->willReturn($page);

        $this->callGetPage('Home')->shouldReturn($page);
    }

    function it_returns_the_page_name()
    {
        $this->callGetName()->shouldReturn('MyPageElement');
    }
}
