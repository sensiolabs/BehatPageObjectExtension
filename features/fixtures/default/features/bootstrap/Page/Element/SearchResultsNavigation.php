<?php

namespace Page\Element;

use SensioLabs\Behat\PageObjectExtension\PageObject\Element;

class SearchResultsNavigation extends Element
{
    /**
     * @var string
     */
    protected $selector = 'div.tabs';

    /**
     * @return boolean
     */
    public function hasTab($name)
    {
        return $this->hasLink($name);
    }
}
