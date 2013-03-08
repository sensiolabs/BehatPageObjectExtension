<?php

namespace spec\SensioLabs\PageObjectExtension\Context;

use PHPSpec2\ObjectBehavior;

require_once __DIR__.'/Fixtures/ArticleList.php';

class PageFactory extends ObjectBehavior
{
    /**
     * @param \Behat\Mink\Session $session
     */
    function let($session)
    {
        $this->beConstructedWith($session, array('base_url' => 'http://behat.dev'));
    }

    function it_should_create_a_page()
    {
        $this->create('Article list')->shouldBeAnInstanceOf('ArticleList');
    }
}
