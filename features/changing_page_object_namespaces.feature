Feature: Changing page object namespaces
  In order to organise my code better
  As a Developer
  I need to define namespaces for my page objects

  Scenario: Configuring both element and page namespaces
    Given a behat configuration:
    """
    default:
      suites:
        default:
          contexts: [SearchContext]
      extensions:
        SensioLabs\Behat\PageObjectExtension:
          namespaces:
            page: Acme\Page
            element: Acme\Element
        Behat\MinkExtension:
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
         * @Then /^I should not see the tabs$/
         */
        public function iShouldSeeTheTabs()
        {
            if ($this->getPage('Homepage')->hasTabs()) {
                throw new \LogicException('Tabs are visible');
            }
        }
    }
    """
    And a page object file "features/bootstrap/Acme/Page/Homepage.php" contains:
    """
    <?php

    namespace Acme\Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class Homepage extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/';

        public function hasTabs()
        {
            return $this->hasElement('Search results navigation');
        }
    }
    """
    And a page object file "features/bootstrap/Acme/Element/SearchResultsNavigation.php" contains:
    """
    <?php

    namespace Acme\Element;

    use Behat\Mink\Exception\ElementNotFoundException;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class SearchResultsNavigation extends Element
    {
        /**
         * @var array $selector
         */
        protected $selector = array('xpath' => '//div[@class="tabs"]');
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
        Then I should not see the tabs
    """
    When I run behat
    Then it should pass

  Scenario: Configuring page namespace only
    Given a behat configuration:
    """
    default:
      suites:
        default:
          contexts: [SearchContext]
      extensions:
        SensioLabs\Behat\PageObjectExtension:
          namespaces:
            page: Acme\Page
        Behat\MinkExtension:
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
         * @Then /^I should not see the tabs$/
         */
        public function iShouldSeeTheTabs()
        {
            if ($this->getPage('Homepage')->hasTabs()) {
                throw new \LogicException('Tabs are visible');
            }
        }
    }
    """
    And a page object file "features/bootstrap/Acme/Page/Homepage.php" contains:
    """
    <?php

    namespace Acme\Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class Homepage extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/';

        public function hasTabs()
        {
            return $this->hasElement('Search results navigation');
        }
    }
    """
    And a page object file "features/bootstrap/Acme/Page/Element/SearchResultsNavigation.php" contains:
    """
    <?php

    namespace Acme\Page\Element;

    use Behat\Mink\Exception\ElementNotFoundException;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class SearchResultsNavigation extends Element
    {
        /**
         * @var array $selector
         */
        protected $selector = array('xpath' => '//div[@class="tabs"]');
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
        Then I should not see the tabs
    """
    When I run behat
    Then it should pass

  Scenario: Configuring multiple namespaces
    Given a behat configuration:
    """
    default:
      suites:
        default:
          contexts: [SearchContext]
      extensions:
        SensioLabs\Behat\PageObjectExtension:
          namespaces:
            page: [Page, Acme\Page]
            element: [Element, Acme\Element]
        Behat\MinkExtension:
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
         * @Then /^I should not see the tabs$/
         */
        public function iShouldSeeTheTabs()
        {
            if ($this->getPage('Homepage')->hasTabs()) {
                throw new \LogicException('Tabs are visible');
            }
        }
    }
    """
    And a page object file "features/bootstrap/Acme/Page/Homepage.php" contains:
    """
    <?php

    namespace Acme\Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class Homepage extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/';

        public function hasTabs()
        {
            return $this->hasElement('Search results navigation');
        }
    }
    """
    And a page object file "features/bootstrap/Element/SearchResultsNavigation.php" contains:
    """
    <?php

    namespace Element;

    use Behat\Mink\Exception\ElementNotFoundException;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class SearchResultsNavigation extends Element
    {
        /**
         * @var array $selector
         */
        protected $selector = array('xpath' => '//div[@class="tabs"]');
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
        Then I should not see the tabs
    """
    When I run behat
    Then it should pass

  Scenario: Configuring multiple namespaces for page only
    Given a behat configuration:
    """
    default:
      suites:
        default:
          contexts: [SearchContext]
      extensions:
        SensioLabs\Behat\PageObjectExtension:
          namespaces:
            page: [Page, Acme\Page]
        Behat\MinkExtension:
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
         * @Then /^I should not see the tabs$/
         */
        public function iShouldSeeTheTabs()
        {
            if ($this->getPage('Homepage')->hasTabs()) {
                throw new \LogicException('Tabs are visible');
            }
        }
    }
    """
    And a page object file "features/bootstrap/Acme/Page/Homepage.php" contains:
    """
    <?php

    namespace Acme\Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class Homepage extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/';

        public function hasTabs()
        {
            return $this->hasElement('Search results navigation');
        }
    }
    """
    And a page object file "features/bootstrap/Acme/Page/Element/SearchResultsNavigation.php" contains:
    """
    <?php

    namespace Acme\Page\Element;

    use Behat\Mink\Exception\ElementNotFoundException;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class SearchResultsNavigation extends Element
    {
        /**
         * @var array $selector
         */
        protected $selector = array('xpath' => '//div[@class="tabs"]');
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
        Then I should not see the tabs
    """
    When I run behat
    Then it should pass
