<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Driver\DriverInterface;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\DriverException;
use Behat\Mink\Selector\SelectorsHandler;
use Behat\Mink\Session;
use PhpSpec\ObjectBehavior;
use SensioLabs\Behat\PageObjectExtension\Context\PageFactoryInterface;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\ElementNotFoundException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\UnexpectedPageException;
use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page as BasePage;

class MyPage extends BasePage
{
    protected $path = '/employees/{employee}';

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
}

class MyPageWithoutPath extends BasePage
{
}

class MyPageWithValidation extends MyPage
{
    protected function verifyPage()
    {
        throw new UnexpectedPageException('Expected to be on "MyPage" but found "Homepage" instead');
    }
}

class MyPageWithUrlValidation extends MyPage
{
    protected function verifyUrl(array $urlParameters = array())
    {
        if (isset($urlParameters['employee'])) {
            throw new UnexpectedPageException(sprintf('Expected to be on "/employee/%s" but found "/other-page" instead', $urlParameters['employee']));
        }
    }
}

class MyPageWithInlineElements extends BasePage
{
    protected $elements = array(
        'Navigation' => array('xpath' => '//div/span[@class="navigation"]'),
        'Search form' => array('css' => 'div.content form#search')
    );

    public function callHasElement($name)
    {
        return $this->hasElement($name);
    }
}

class PageSpec extends ObjectBehavior
{
    function let(Session $session, PageFactoryInterface $factory, SelectorsHandler $selectorsHandler, DriverInterface $driver)
    {
        // until we have proper abstract class support in PhpSpec
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPage');
        $this->beConstructedWith($session, $factory);

        $session->getSelectorsHandler()->willReturn($selectorsHandler);
        $session->getDriver()->willReturn($driver);

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

    function it_prepends_base_url($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev/'));

        $session->visit('http://behat.dev/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(200);

        $this->open(array('employee' => 13))->shouldReturn($this);
    }

    function it_cleans_up_slashes($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev/'));

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
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithValidation');
        $this->beConstructedWith($session, $factory);

        $session->visit('/employees/13')->shouldBeCalled();
        $session->getStatusCode()->willReturn(200);

        $this->shouldThrow(new UnexpectedPageException('Expected to be on "MyPage" but found "Homepage" instead'))->duringOpen(array('employee' => 13));
    }

    function it_optionally_verifies_the_url($session, $factory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithUrlValidation');
        $this->beConstructedWith($session, $factory);

        $session->visit('/employees/13')->willReturn();
        $session->getStatusCode()->willReturn(200);

        $this->shouldThrow(new UnexpectedPageException('Expected to be on "/employee/13" but found "/other-page" instead'))->duringOpen(array('employee' => 13));
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

    function it_creates_an_inline_element_if_present($session, $factory, $selectorsHandler, $driver, InlineElement $element, NodeElement $node)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithInlineElements');
        $this->beConstructedWith($session, $factory);

        $elementLocator = '//div/span[@class="navigation"]';

        $element->getXpath()->willReturn($elementLocator);
        $selectorsHandler->selectorToXpath('xpath', $elementLocator)->willReturn($elementLocator);
        $driver->find('//html'.$elementLocator)->willReturn($node);

        $factory->createInlineElement(array('xpath' => $elementLocator))->willReturn($element);

        $this->getElement('Navigation')->shouldReturn($element);
        $this->callHasElement('Navigation')->shouldReturn(true);
    }

    function it_throws_an_exception_if_locator_does_not_evaluate_to_a_node_with_an_inline_element($session, $factory, $selectorsHandler, $driver, InlineElement $element, NodeElement $node)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithInlineElements');
        $this->beConstructedWith($session, $factory);

        $elementLocator = '//div/span[@class="navigation"]';

        $element->getXpath()->willReturn($elementLocator);
        $selectorsHandler->selectorToXpath('xpath', $elementLocator)->willReturn($elementLocator);
        $driver->find('//html'.$elementLocator)->willReturn(null);

        $factory->createInlineElement(array('xpath' => $elementLocator))->willReturn($element);

        $this->shouldThrow(new ElementNotFoundException('"Navigation" element is not present on the page'))->duringGetElement('Navigation');
        $this->callHasElement('Navigation')->shouldReturn(false);
    }

    function it_returns_the_page_name()
    {
        $this->callGetName()->shouldReturn('MyPage');
    }

    function it_confirms_the_open_page_is_open($session)
    {
        $session->getStatusCode()->willReturn(200);

        $this->isOpen()->shouldReturn(true);
    }

    function it_confirms_the_page_is_not_open_on_error($session)
    {
        $session->getStatusCode()->willReturn(404);

        $this->isOpen()->shouldReturn(false);
    }

    function it_confirms_the_page_is_not_open_if_another_page_is_open_instead($session, $factory)
    {
        $this->beAnInstanceOf('spec\SensioLabs\Behat\PageObjectExtension\PageObject\MyPageWithUrlValidation');
        $this->beConstructedWith($session, $factory);

        $session->getStatusCode()->willReturn(200);

        $this->isOpen(array('employee' => 13))->shouldReturn(false);
    }
}
