<?php

use Behat\Mink\Session;

use SensioLabs\PageObjectExtension\PageObject\Element;

class SearchBox extends Element
{
    protected $selector =  array('xpath' => '//div[@id="search"]');
}
