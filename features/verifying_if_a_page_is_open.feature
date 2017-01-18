Feature: Verifying if a page is open
  In order to avoid endless debugging nights
  As a Developer
  I need to check if a page is open

  Scenario: Verifying if a page is open (without parameters)
    Given I configured the page object extension
    And a context file "features/bootstrap/SearchContext.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

    class SearchContext extends PageObjectContext
    {
        /**
         * @Given /^I visited the news list$/
         */
        public function iVisitedTheNewsList()
        {
            $this->getPage('News list')->open();
        }

        /**
         * @When /^I should see a list of recent news articles$/
         */
        public function iShouldSeeListOfRecentNewsArticles()
        {
            $isNewsListOpen = $this->getPage('News list')->isOpen();

            if (!$isNewsListOpen) {
                throw new \LogicException('Expected the news list page to be open');
            }
        }
    }
    """
    And a page object file "features/bootstrap/Page/NewsList.php" contains:
    """
    <?php

    namespace Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class NewsList extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/news';

        protected function verifyResponse()
        {
            // no exception
        }

        protected function verifyPage()
        {
            // no exception
        }

        protected function verifyUrl(array $urlParameters = array())
        {
            // no exception
        }
    }
    """
    And a feature file "features/news.feature" contains:
    """
    Feature: Viewing the news list
      In order to find news I might be interested in
      As a Visitor
      I want to view a list of news

      Scenario: Viewing recent news articles
        Given I visited the news list
         Then I should see a list of recent news articles
    """
    When I run behat
    Then it should pass with:
    """
    ..

    1 scenario (1 passed)
    2 steps (2 passed)
    """

  Scenario: Verifying if a page is open (with an url)
    Given I configured the page object extension
    And a context file "features/bootstrap/SearchContext.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

    class SearchContext extends PageObjectContext
    {
        /**
         * @Given /^I visited a news page$/
         */
        public function iVisitedNewsPage()
        {
            // opening a wrong page
            $this->getPage('News')->open(array('slug' => 'page-object-extension-2.0-released'));
        }

        /**
         * @When /^I should see the news article$/
         */
        public function isShouldSeeTheNewsArticle()
        {
            $isNewsOpen = $this->getPage('News')->isOpen(array('behat-3-released'));

            if (!$isNewsOpen) {
                throw new \LogicException('Expected the news page to be open');
            }
        }
    }
    """
    And a page object file "features/bootstrap/Page/News.php" contains:
    """
    <?php

    namespace Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class News extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/news/{slug}';

        protected function verifyUrl(array $urlParameters = array())
        {
            if ($this->getUrl($urlParameters) !== $this->getDriver()->getCurrentUrl()) {
                throw new \LogicException(sprintf('The current url "%s" does not match the expected "%s"', $this->getDriver()->getCurrentUrl(), $this->getUrl($urlParameters)));
            }
        }
    }
    """
    And a feature file "features/news.feature" contains:
    """
    Feature: Reading news
      In order to be up to date with recent events
      As a Visitor
      I want to read news articles

      Scenario: Reading a news article
        Given I visited a news page
         Then I should see the news article
    """
    When I run behat
    Then it should fail with:
    """
    .F
    """

  Scenario: Verifying if a page is open (with its contents)
    Given I configured the page object extension
    And a context file "features/bootstrap/SearchContext.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

    class SearchContext extends PageObjectContext
    {
        /**
         * @Given /^I visited the news list$/
         */
        public function iVisitedTheNewsList()
        {
            try {
                $this->getPage('News list')->open();
            } catch (\Exception $e) {
                // Opening a page with open() would trigger verification if page is open.
                // We want to verify the behavior is also trigger from isOpen().
            }
        }

        /**
         * @When /^I should see a list of recent news articles$/
         */
        public function iShouldSeeListOfRecentNewsArticles()
        {
            $isNewsListOpen = $this->getPage('News list')->isOpen();

            if (!$isNewsListOpen) {
                throw new \LogicException('Expected the news list page to be open');
            }
        }
    }
    """
    And a page object file "features/bootstrap/Page/NewsList.php" contains:
    """
    <?php

    namespace Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class NewsList extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/news';

        /**
         * @return boolean
         */
        protected function verifyPage()
        {
            throw new \InvalidArgumentException('The page does not look like a news list page');
        }
    }
    """
    And a feature file "features/news.feature" contains:
    """
    Feature: Viewing the news list
      In order to find news I might be interested in
      As a Visitor
      I want to view a list of news

      Scenario: Viewing recent news articles
        Given I visited the news list
         Then I should see a list of recent news articles
    """
    When I run behat
    Then it should fail with:
    """
    .F
    """

  Scenario: Verifying if a page is open (with the response)
    Given I configured the page object extension
    And a context file "features/bootstrap/SearchContext.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

    class SearchContext extends PageObjectContext
    {
        /**
         * @Given /^I visited the news list$/
         */
        public function iVisitedTheNewsList()
        {
            try {
                $this->getPage('News list')->open();
            } catch (\Exception $e) {
                // Opening a page with open() would trigger verification if response is valid.
                // We want to verify the behavior is also trigger from isOpen().
            }
        }

        /**
         * @When /^I should see a list of recent news articles$/
         */
        public function iShouldSeeListOfRecentNewsArticles()
        {
            $isNewsListOpen = $this->getPage('News list')->isOpen();

            if (!$isNewsListOpen) {
                throw new \LogicException('Expected the news list page to be open');
            }
        }
    }
    """
    And a page object file "features/bootstrap/Page/NewsList.php" contains:
    """
    <?php

    namespace Page;

    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

    class NewsList extends Page
    {
        /**
         * @var string $path
         */
        protected $path = '/news';

        /**
         * @return boolean
         */
        protected function verifyResponse()
        {
            throw new \InvalidArgumentException('The request to the News List did not return a successful response');
        }
    }
    """
    And a feature file "features/news.feature" contains:
    """
    Feature: Viewing the news list
      In order to find news I might be interested in
      As a Visitor
      I want to view a list of news

      Scenario: Viewing recent news articles
        Given I visited the news list
         Then I should see a list of recent news articles
    """
    When I run behat
    Then it should fail with:
    """
    .F
    """
