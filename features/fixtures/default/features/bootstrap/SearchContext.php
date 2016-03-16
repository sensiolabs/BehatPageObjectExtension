<?php

use Behat\Behat\Context\Context;
use Page\Homepage;
use Page\Element\SearchResultsNavigation;

class SearchContext implements Context
{
    /**
     * @var Homepage
     */
    private $homepage;

    /**
     * @var SearchResultsNavigation
     */
    private $searchResultsNavigation;

    /**
     * @param Homepage                $homepage
     * @param SearchResultsNavigation $searchResultsNavigation
     */
    public function __construct(Homepage $homepage, SearchResultsNavigation $searchResultsNavigation)
    {
        $this->homepage = $homepage;
        $this->searchResultsNavigation = $searchResultsNavigation;
    }

    /**
     * @Given /^I visited the homepage$/
     */
    public function iVisitedTheHomepage()
    {
        $this->homepage->open();
    }

    /**
     * @When /^I should not see the "(?P<tab>[^"]*)" tab$/
     */
    public function iShouldSeeNotTheTab($tab)
    {
        if ($this->searchResultsNavigation->hasTab($tab)) {
            throw new \LogicException(sprintf('%s tab is present on the page', $tab));
        }
    }
}
