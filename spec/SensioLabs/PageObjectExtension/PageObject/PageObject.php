<?php

namespace spec\SensioLabs\PageObjectExtension\PageObject;

use PHPSpec2\ObjectBehavior;

class PageObject extends ObjectBehavior
{
    /**
     * @param \Behat\Mink\Session $session
     */
    function let($session)
    {
        $this->beConstructedWith($session);
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

    function it_prepends_base_url($session)
    {
        $this->beConstructedWith($session, array('base_url' => 'http://behat.dev/'));

        $session->visit('http://behat.dev/employees')->shouldBeCalled();

        $this->open('employees')->shouldReturn($this);
    }

    function it_cleans_up_slashes($session)
    {
        $this->beConstructedWith($session, array('base_url' => 'http://behat.dev/'));

        $session->visit('http://behat.dev/employees')->shouldBeCalled();

        $this->open('/employees')->shouldReturn($this);
    }

}
