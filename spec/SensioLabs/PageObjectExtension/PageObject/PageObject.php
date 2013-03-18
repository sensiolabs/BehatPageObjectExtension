<?php

namespace spec\SensioLabs\PageObjectExtension\PageObject;

use PHPSpec2\ObjectBehavior;

class PageObject extends ObjectBehavior
{
    /**
     * @param \Behat\Mink\Session                                          $session
     * @param \SensioLabs\PageObjectExtension\Context\PageFactoryInterface $factory
     */
    function let($session, $factory)
    {
        $this->beConstructedWith($session, $factory);
    }

    function it_should_be_a_document_element()
    {
        $this->shouldHaveType('Behat\Mink\Element\DocumentElement');
    }

    function it_opens_a_relative_path($session)
    {
        $session->visit('/employees')->shouldBeCalled();

        $this->open('/employees')->shouldReturn($this);
    }

    function it_opens_an_absolute_path($session)
    {
        $session->visit('http://localhost/employees')->shouldBeCalled();

        $this->open('http://localhost/employees')->shouldReturn($this);
    }

    function it_prepends_base_url($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev/'));

        $session->visit('http://behat.dev/employees')->shouldBeCalled();

        $this->open('employees')->shouldReturn($this);
    }

    function it_cleans_up_slashes($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev/'));

        $session->visit('http://behat.dev/employees')->shouldBeCalled();

        $this->open('/employees')->shouldReturn($this);
    }

    function it_gives_clear_feedback_if_method_is_invalid($session, $factory)
    {
        $this->beConstructedWith($session, $factory, array('base_url' => 'http://behat.dev/'));

        $this->shouldThrow(new \BadMethodCallException('"search" method is not available on the PageObject'))->during('search');
    }
}
