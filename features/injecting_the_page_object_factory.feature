Feature: Injecting a page object factory
  In order to avoid extending third-party contexts
  As a Developer
  I need other means of injecting the page object factory

  Scenario: Delegating page interactions to page objects
    Given I configured the page object extension
    And a context file "features/bootstrap/SearchContext.php" contains:
    """
    <?php

    use Behat\Behat\Context\BehatContext;
    use SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface;
    use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
    use SensioLabs\Behat\PageObjectExtension\Context\PageFactory;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class SearchContext extends BehatContext implements PageObjectAwareInterface
    {
        private $factory;

        /**
         * @param PageFactory $pageFactory
         */
        public function setPageFactory(PageFactory $factory)
        {
            $this->factory = $factory;
        }

        /**
         * @Given /^I visited the homepage$/
         */
        public function iVisitedTheHomepage()
        {
            $this->getPage('Homepage')->open();
        }

        /**
         * @Then /^I should be able to search$/
         */
        public function iShouldBeAbleToSearch()
        {
            if (!$this->getPage('Homepage')->hasSearchForm()) {
                throw new \LogicException('Could not find the search form');
            }
        }

        /**
         * @return Page
         */
        private function getPage($name)
        {
            return $this->factory->createPage($name);
        }
    }
    """
    And a page object file "features/bootstrap/Page/Homepage.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class Homepage extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/';

        /**
         * @var array $elements
         */
        protected $elements = array(
            'Search form' => array('css' => 'form[name="search"]')
        );

        /**
         * @return boolean
         */
        public function hasSearchForm()
        {
            return $this->hasElement('Search form');
        }
    }
    """
    And a feature file "features/search.feature" contains:
    """
    Feature: Search
      In order to find lolcats
      As a Cat Lover
      I want to search the internetz

      Scenario: Searching for lolcats
        Given I visited the homepage
         Then I should be able to search
    """
    When I run behat
    Then it should pass
