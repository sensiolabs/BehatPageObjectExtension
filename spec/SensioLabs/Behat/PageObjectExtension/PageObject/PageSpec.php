<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page as BasePage;

class MyPage extends BasePage
{
    protected $path = '/employees/{employee}';

    protected $elements = array(
        'Primary Navigation' => array('xpath' => '//div/span[@class="navigation"]'),
        'Search form' => array('css' => 'div.content form#search')
    );

    public function callGetPage($name)
    {
        return $this->getPage($name);
    }

    public function callGetName()
    {
        return $this->getName();
    }

    public function callHasElement($name)
    {
        return $this->hasElement($name);
    }

    public function callGetUrl(array $urlParameters = array())
    {
        return $this->getUrl($urlParameters);
    }

    protected function verifyUrl(array $urlParameters = array())
    {
    }
}

class MyPageWithoutPath extends BasePage
{
}

class MyPageWithPageValidation extends MyPage
{
    protected function verifyPage()
    {
        throw new UnexpectedPageException('Expected to be on "MyPage" but found "Homepage" instead');
    }
}

class MyPageWithDefaultUrlValidation extends BasePage
{
    protected $path = '/employees/{employee}';
}

class PageSpec extends ObjectBehavior
{
    function let(Session $session, Factory $factory, SelectorsHandler $selectorsHandler, DriverInterface $driver)
    {
        // until we have proper abstract class support in PhpSpec
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPage');
        $this->beConstructedWith($session, $factory);

        $session->getSelectorsHandler()->willReturn($selectorsHandler);
        $session->getDriver()->willReturn($driver);
        $session->getCurrentUrl()->willReturn('http://localhost/employees/13');
        $session->getStatusCode()->willReturn(200);
    }

    function it_should_be_a_page_object()
    {
        $this->shouldHaveType('SensioLabs\Behat\PageObjectExtension\PageObject\PageObject');
    }

    function it_should_be_a_document_element()
    {
        $this->shouldHaveType('Behat\Mink\Element\DocumentElement');
    }

    function it_opens_a_relative_path($session)
    {
        $session->visit('/employees/13')->shouldBeCalled();

        $this->open(array('employee' => 13))->shouldReturn($this);
    }

    function it_prepends_base_url($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev'));

        $session->visit('http://behat.dev/employees/13')->shouldBeCalled();

        $this->open(array('employee' => 13))->shouldReturn($this);
    }

    function it_cleans_up_slashes($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev/'));

        $session->visit('http://behat.dev/employees/13')->shouldBeCalled();

        $this->open(array('employee' => 13))->shouldReturn($this);
    }

    function it_leaves_placeholders_if_not_provided($session)
    {
        $session->getCurrentUrl()->willReturn('http://localhost/employees/{employee}');

        $session->visit('/employees/{employee}')->shouldBeCalled();

        $this->open()->shouldReturn($this);
    }

    function it_requires_path_to_open_a_page($session, $factory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithoutPath');
        $this->beConstructedWith($session, $factory);

        $this->shouldThrow(new PathNotProvidedException('You must add a path property to your page object'))
            ->duringOpen();
    }

    function it_provides_a_way_to_generate_an_url_for_extension($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev/'));

        $this->callGetUrl(array('employee' => 13))->shouldReturn('http://behat.dev/employees/13');
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

    function it_optionally_verifies_the_page($session, $factory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithPageValidation');
        $this->beConstructedWith($session, $factory);

        $session->visit('/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(200);

        $this->shouldThrow(new UnexpectedPageException('Expected to be on "MyPage" but found "Homepage" instead'))->duringOpen(array('employee' => 13));
    }

    function it_verifies_the_url($session, $factory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithDefaultUrlValidation');
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://localhost/'));

        $session->getCurrentUrl()->willReturn('http://localhost/employees/14');
        $session->visit(Argument::any())->willReturn();

        $this->shouldThrow(new UnexpectedPageException('Expected to be on "http://localhost/employees/13" but found "http://localhost/employees/14" instead'))
            ->duringOpen(array('employee' => 13));
    }

    function it_gives_clear_feedback_if_method_is_invalid($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev/'));

        $this->shouldThrow(new \BadMethodCallException('"search" method is not available on the MyPage'))->during('search');
    }

    function it_creates_a_page($factory, BasePage $page)
    {
        $factory->createPage('Home')->willReturn($page);

        $this->callGetPage('Home')->shouldReturn($page);
    }

    function it_creates_an_element($selectorsHandler, $driver, $factory, Element $element, NodeElement $node)
    {
        $elementLocator = '//p[@id="navigation"]';

        $element->getXpath()->willReturn($elementLocator);
        $selectorsHandler->selectorToXpath('xpath', $elementLocator)->willReturn($elementLocator);
        $driver->find('//html'.$elementLocator)->willReturn($node);

        $factory->createElement('Navigation')->willReturn($element);

        $this->getElement('Navigation')->shouldReturn($element);
        $this->callHasElement('Navigation')->shouldReturn(true);
    }

    function it_throws_an_exception_if_locator_does_not_evaluate_to_a_node($selectorsHandler, $driver, $factory, Element $element)
    {
        $elementLocator = '//p[@id="navigation"]';

        $element->getXpath()->willReturn($elementLocator);
        $selectorsHandler->selectorToXpath('xpath', $elementLocator)->willReturn($elementLocator);
        $driver->find('//html'.$elementLocator)->willReturn(null);

        $factory->createElement('Navigation')->willReturn($element);

        $this->shouldThrow(new ElementNotFoundException('"Navigation" element is not present on the page'))->duringGetElement('Navigation');
        $this->callHasElement('Navigation')->shouldReturn(false);
    }

    function it_creates_an_inline_element_if_present($factory, $selectorsHandler, $driver, InlineElement $element, NodeElement $node)
    {
        $elementLocator = '//div/span[@class="navigation"]';

        $element->getXpath()->willReturn($elementLocator);
        $selectorsHandler->selectorToXpath('xpath', $elementLocator)->willReturn($elementLocator);
        $driver->find('//html'.$elementLocator)->willReturn($node);

        $factory->createInlineElement(array('xpath' => $elementLocator))->willReturn($element);

        $this->getElement('Primary Navigation')->shouldReturn($element);
        $this->callHasElement('Primary Navigation')->shouldReturn(true);
    }

    function it_throws_an_exception_if_locator_does_not_evaluate_to_a_node_with_an_inline_element($factory, $selectorsHandler, $driver, InlineElement $element, NodeElement $node)
    {
        $elementLocator = '//div/span[@class="navigation"]';

        $element->getXpath()->willReturn($elementLocator);
        $selectorsHandler->selectorToXpath('xpath', $elementLocator)->willReturn($elementLocator);
        $driver->find('//html'.$elementLocator)->willReturn(null);

        $factory->createInlineElement(array('xpath' => $elementLocator))->willReturn($element);

        $this->shouldThrow(new ElementNotFoundException('"Primary Navigation" element is not present on the page'))->duringGetElement('Primary Navigation');
        $this->callHasElement('Primary Navigation')->shouldReturn(false);
    }

    function it_returns_the_page_name()
    {
        $this->callGetName()->shouldReturn('MyPage');
    }

    function it_confirms_the_open_page_is_open($session)
    {
        $session->getStatusCode()->willReturn(200);
        $session->getCurrentUrl()->willReturn('http://localhost/employees/13');

        $this->isOpen(array('employee' => 13))->shouldReturn(true);
    }

    function it_confirms_the_page_is_not_open_on_error($session)
    {
        $session->getStatusCode()->willReturn(404);
        $session->getCurrentUrl()->willReturn('http://localhost/employees/13');

        $this->isOpen(array('employee' => 13))->shouldReturn(false);
    }
}
