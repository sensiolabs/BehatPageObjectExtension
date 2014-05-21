Feature: Using page objects with a factory
  In order to keep my context files maintainable
  As a Developer
  I need to encapsulate knowledge about pages in page objects

  Scenario: Using a factory to create page objects
    Given a behat configuration:
    """
    default:
      context:
        class: SearchContext
      extensions:
        SensioLabs\Behat\PageObjectExtension\Extension: ~
        Behat\MinkExtension\Extension:
          goutte: ~
          base_url: http://localhost:8000
    """
    And a context file "features/bootstrap/SearchContext.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

    class SearchContext extends PageObjectContext
    {
        /**
         * @Given /^I visited the homepage$/
         */
        public function iVisitedTheHomepage()
        {
            $this->getPage('Homepage')->open();
        }

        /**
         * @When /^I search for "(?P<keywords>[^"]*)"$/
         */
        public function iSearchFor($keywords)
        {
            $this->getPage('Homepage')->search($keywords);
        }

        /**
         * @When /^I change the tab to "(?P<tab>[^"]*)"$/
         */
        public function iChangeTheTabTo($tab)
        {
            $this->getPage('Web search results')->switchTab($tab);
        }

        /**
         * @Then /^I should see a list of "(?P<keywords>[^"]*)" websites$/
         */
        public function iShouldSeeAListOfWebsites($keywords)
        {
            $resultCount = $this->getPage('Web search results')->countResults($keywords);

            if ($resultCount < 1) {
                throw new \LogicException('Expected at least one search result');
            }
        }

        /**
         * @Then /^I should see a list of "(?P<keywords>[^"]*)" images$/
         */
        public function iShouldSeeAListOfImages($keywords)
        {
            $resultCount = $this->getPage('Images search results')->countResults($keywords);

            if ($resultCount < 1) {
                throw new \LogicException('Expected at least one search result');
            }
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
         * @param string $keywords
         *
         * @return Page
         */
        public function search($keywords)
        {
            $element = $this->getElement('Search form');
            $element->fillField('query', $keywords);
            $element->pressButton('Search');

            return $this->getPage('Web search results');
        }
    }
    """
    And a page object file "features/bootstrap/Page/WebSearchResults.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class WebSearchResults extends Page
    {
        /**
         * @param string $keywords
         *
         * @return integer
         */
        public function countResults($keywords)
        {
            $xpath = sprintf(
                '//h3[contains(translate(.,"abcdefghijklmnopqrstuvwxyz","ABCDEFGHIJKLMNOPQRSTUVWXYZ"), "%s") and @class="result-item"]/a',
                strtoupper($keywords)
            );
            $results = $this->findAll('xpath', $xpath);

            return count($results);
        }

        /**
         * @param string $name
         *
         * @return Page
         */
        public function switchTab($name)
        {
            return $this->getElement('Search results navigation')->switchTab($name);
        }
    }
    """
    And a page object file "features/bootstrap/Page/ImagesSearchResults.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class ImagesSearchResults extends Page
    {
        /**
         * @param string $keywords
         *
         * @return integer
         */
        public function countResults($keywords)
        {
            $xpath = sprintf(
                '//img[@class="result-item" and contains(translate(@alt,"abcdefghijklmnopqrstuvwxyz","ABCDEFGHIJKLMNOPQRSTUVWXYZ"), "%s")]',
                strtoupper($keywords)
            );
            $results = $this->findAll('xpath', $xpath);

            return count($results);
        }
    }
    """
    And a page object file "features/bootstrap/Page/Element/SearchResultsNavigation.php" contains:
    """
    <?php

    use Behat\Mink\Exception\ElementNotFoundException;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class SearchResultsNavigation extends Element
    {
        /**
         * @var array $selector
         */
        protected $selector = array('xpath' => '//div[@class="tabs"]');

        /**
         * @param string $name
         *
         * @return Page
         */
        public function switchTab($name)
        {
            $tab = $this->find('xpath', sprintf('//a[contains(., "%s")]', $name));

            if (!$tab) {
                throw new ElementNotFoundException($this->getSession(), 'tab', 'name', $name);
            }

            $tab->click();

            return $this->getPage($name.' search results');
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
         When I search for "lolcats"
         Then I should see a list of "lolcat" websites

      Scenario: Searching for lolcat images
        Given I visited the homepage
          And I search for "lolcats"
         When I change the tab to "Images"
         Then I should see a list of "lolcat" images
    """
    When I run behat
    Then it should pass with:
    """
    ...

    2 scenarios (2 passed)
    7 steps (7 passed)
    """