Feature: Injecting a page object directly into the context
  In order to make page object dependencies visible
  As a Developer
  I need to inject a page object directly into the context's constructor

  Scenario: Injecting a page object via a constructor
    Given I configured the page object extension
    And a context file "features/bootstrap/SearchContext.php" contains:
    """
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
    """
    And a page object file "features/bootstrap/Page/Homepage.php" contains:
    """
    <?php

    namespace Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class Homepage extends Page
    {
        /**
         * @var string
         */
        protected $path = '/';
    }
    """
    And a page object file "features/bootstrap/Page/Element/SearchResultsNavigation.php" contains:
    """
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
    """
    And a feature file "features/search.feature" contains:
    """
    Feature: Search
      In order to find lolcats
      As a Cat Lover
      I want to search the internetz

      Scenario: Searching for lolcats
        Given I visited the homepage
         Then I should not see the "Images" tab
    """
    When I run behat
    Then it should pass

  Scenario: Configuring the generated proxy location
    Given a behat configuration:
    """
    default:
      suites:
        default:
          contexts: [SearchContext]
      extensions:
        SensioLabs\Behat\PageObjectExtension:
          factory:
            proxies_target_dir: %paths.base%/tmp/
        Behat\MinkExtension:
          goutte: ~
          base_url: http://localhost:8000
    """
    And a feature with a context file that uses page objects
    When I run behat
    Then it should pass
    And the proxies should be generated in the "tmp" directory
