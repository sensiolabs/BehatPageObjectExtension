<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Exception\DriverException;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page as BasePage;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\CssSelector;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Selector\XpathSelector;

class MyPage extends BasePage
{
    protected $path = '/employees/{employee}';

    public function getSelectors()
    {
        return $this->selectors;
    }

    public function callGetPage($name)
    {
        return $this->getPage($name);
    }

    public function callGetElement($name)
    {
        return $this->getElement($name);
    }

    public function callGetName()
    {
        return $this->getName();
    }
}

class MyPageWithoutPath extends BasePage
{
    public function getSelectors()
    {
        return $this->selectors;
    }
}

class MyPageWithValidation extends MyPage
{
    public function getSelectors()
    {
        return $this->selectors;
    }

    protected function verifyPage()
    {
        throw new UnexpectedPageException('Expected to be on "MyPage" but found "Homepage" instead');
    }
}

class MyPageWithInlineElements extends BasePage
{
    protected $elements = array(
        'Navigation' => array('xpath' => '//div/span[@class="navigation"]'),
        'Search form' => array('css' => 'div.content form#search')
    );

    public function getSelectors()
    {
        return $this->selectors;
    }

    public function callGetElement($name)
    {
        return $this->getElement($name);
    }

    protected function createSelectors()
    {
        $this->addSelector('Menu', new CssSelector('div.menu'));
        $this->addSelector('Footer links', new XpathSelector('//div/ul/li/a'));
    }
}

class MyPageWithInlineElementsObjectSelectorsOnly extends BasePage
{
    public function getSelectors()
    {
        return $this->selectors;
    }

    public function callGetElement($name)
    {
        return $this->getElement($name);
    }

    protected function createSelectors()
    {
        $this->addSelector('Menu', new CssSelector('div.menu'));
        $this->addSelector('Footer links', new XpathSelector('//div/ul/li/a'));
    }
}

// this class is created only for constructor exceptions tests
abstract class MyPageWithInvalidInlineElementsWrapper extends BasePage
{
    public function __construct($callParent = false, Session $session = null, PageFactoryInterface $pageFactory = null,
        SelectorFactoryInterface $selectorFactory = null, array $parameters = array())
    {
        if ($callParent) {
            parent::__construct($session, $pageFactory, $selectorFactory, $parameters);
        }
    }
}

class PageSpec extends ObjectBehavior
{
    function let(Session $session, PageFactoryInterface $factory, SelectorFactoryInterface $selectorFactory, SelectorInterface $selector)
    {
        // until we have proper abstract class support in PhpSpec
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPage');
        $this->beConstructedWith($session, $factory, $selectorFactory);

        $selectorFactory->create(Argument::any())->willReturn($selector);
    }

    function it_should_be_a_document_element()
    {
        $this->shouldHaveType('Behat\Mink\Element\DocumentElement');
    }

    function it_opens_a_relative_path($session)
    {
        $session->visit('/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(200);

        $this->open(array('employee' => 13))->shouldReturn($this);
    }

    function it_prepends_base_url($session, $factory, $selectorFactory)
    {
        $this->beConstructedWith($session, $factory, $selectorFactory, array('base_url' => 'http://behat.dev/'));

        $session->visit('http://behat.dev/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(200);

        $this->open(array('employee' => 13))->shouldReturn($this);
    }

    function it_cleans_up_slashes($session, $factory, $selectorFactory)
    {
        $this->beConstructedWith($session, $factory, $selectorFactory, array('base_url' => 'http://behat.dev/'));

        $session->visit('http://behat.dev/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(200);

        $this->open(array('employee' => 13))->shouldReturn($this);
    }

    function it_leaves_placeholders_if_not_provided($session)
    {
        $session->visit('/employees/{employee}')->shouldBeCalled();
        $session->getStatusCode()->willReturn(200);

        $this->open()->shouldReturn($this);
    }

    function it_requires_path_to_open_a_page($session, $factory, $selectorFactory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithoutPath');
        $this->beConstructedWith($session, $factory, $selectorFactory);

        $this->shouldThrow(new PathNotProvidedException('You must add a path property to your page object'))
            ->duringOpen();
    }

    function it_verifies_client_error_status_code_if_available($session, $factory)
    {
        $session->visit('/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(404);
        $session->getCurrentUrl()->willReturn('/employees/13');

        $this->shouldThrow(new UnexpectedPageException('Could not open the page: "/employees/13". Received an error status code: 404'))
            ->duringOpen(array('employee' => 13));
    }

    function it_verifies_server_error_status_code_if_available($session)
    {
        $session->visit('/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(500);
        $session->getCurrentUrl()->willReturn('/employees/13');

        $this->shouldThrow(new UnexpectedPageException('Could not open the page: "/employees/13". Received an error status code: 500'))
            ->duringOpen(array('employee' => 13));
    }

    function it_skips_status_code_check_if_driver_does_not_support_it($session)
    {
        $session->visit('/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willThrow(new DriverException(''));

        $this->open(array('employee' => 13))->shouldReturn($this);
    }

    function it_optionally_verifies_the_page($session, $factory, $selectorFactory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithValidation');
        $this->beConstructedWith($session, $factory, $selectorFactory);

        $session->visit('/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(200);

        $this->shouldThrow(new UnexpectedPageException('Expected to be on "MyPage" but found "Homepage" instead'))->duringOpen(array('employee' => 13));
    }

    function it_gives_clear_feedback_if_method_is_invalid($session, $factory, $selectorFactory)
    {
        $this->beConstructedWith($session, $factory, $selectorFactory, array('base_url' => 'http://behat.dev/'));

        $this->shouldThrow(new \BadMethodCallException('"search" method is not available on the MyPage'))->during('search');
    }

    function it_creates_a_page($factory, BasePage $page)
    {
        $factory->createPage('Home')->willReturn($page);

        $this->callGetPage('Home')->shouldReturn($page);
    }

    function it_creates_an_element($factory, Element $element)
    {
        $factory->createElement('Navigation')->willReturn($element);

        $this->callGetElement('Navigation')->shouldReturn($element);
    }

    function it_creates_an_inline_element_if_present($session, $factory, $selectorFactory, InlineElement $element, SelectorInterface $selector)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithInlineElements');
        $this->beConstructedWith($session, $factory, $selectorFactory);

        $this->create_selector($selector, 'xpath', '//div/span[@class="navigation"]');

        $factory->createInlineElement(Argument::type('SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorInterface'))
            ->willReturn($element);

        $this->callGetElement('Navigation')->shouldReturn($element);
    }

    function it_returns_the_page_name()
    {
        $this->callGetName()->shouldReturn('MyPage');
    }

    function it_converts_array_elements_into_selector_instances($session, $factory, $selectorFactory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithInlineElements');
        $this->beConstructedWith($session, $factory, $selectorFactory);

        $this->getSelectors()->shouldBeAnInstanceOf('Symfony\Component\DependencyInjection\ParameterBag\ParameterBag');
        $this->getSelectors()->get('Navigation')->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorInterface');
        $this->getSelectors()->get('Search form')->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\Selector\SelectorInterface');
        $this->getSelectors()->shouldHaveSelectorsCount(4);
    }

    function it_merges_elements_with_object_selectors($session, $factory, $selectorFactory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithInlineElements');
        $this->beConstructedWith($session, $factory, $selectorFactory);

        $this->getSelectors()->get('Menu')->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\Selector\CssSelector');
        $this->getSelectors()->get('Footer links')->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\Selector\XpathSelector');
        $this->getSelectors()->shouldHaveSelectorsCount(4);
    }

    function it_creates_selectors_from_method($session, $factory, $selectorFactory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithInlineElementsObjectSelectorsOnly');
        $this->beConstructedWith($session, $factory, $selectorFactory);

        $this->getSelectors()->get('Menu')->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\Selector\CssSelector');
        $this->getSelectors()->get('Footer links')->shouldBeAnInstanceOf('SensioLabs\Behat\PageObjectExtension\PageObject\Selector\XpathSelector');
        $this->getSelectors()->shouldHaveSelectorsCount(2);
    }


    public function getMatchers()
    {
        return array(
            'haveSelectorsCount' => function($subject, $expected) {
                return (count($subject->all()) === $expected);
            }
        );
    }

    private function create_selector(SelectorInterface $selector, $type, $path)
    {
        $selector->getPath()->willReturn($path);
        $selector->getType()->willReturn($type);
        $selector->asArray()->willReturn(array($type => $path));
    }
}
