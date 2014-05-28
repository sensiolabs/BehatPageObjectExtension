<?php

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class SearchBox extends Element
{
    protected $selector =  array('xpath' => '//div[@id="search"]');
}
