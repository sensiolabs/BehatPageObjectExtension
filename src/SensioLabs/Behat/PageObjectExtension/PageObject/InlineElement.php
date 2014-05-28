<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject;

use Behat\Mink\Session;

class InlineElement extends Element
{
    /**
     * @var array|string
     */
    protected $selector = null;

    /**
     * @param array|string $selector
     * @param Session      $session
     * @param Factory      $factory
     */
    public function __construct($selector, Session $session, Factory $factory)
    {
        $this->selector = $selector;

        parent::__construct($session, $factory);
    }
}
