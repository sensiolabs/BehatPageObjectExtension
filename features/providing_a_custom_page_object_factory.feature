Feature: Providing a custom page object factory
  In order to customise the way page objects are created
  As an Experienced Developer
  I need to provide my own implementation of the page object factory

  Scenario: Custom page object factory
    Given a behat configuration:
    """
    default:
      suites:
        default:
          contexts: [SearchContext]
      extensions:
        SensioLabs\Behat\PageObjectExtension:
          factory: acme.page_object.factory
        acme_page_object_extension.php: ~
        Behat\MinkExtension:
          goutte: ~
          base_url: http://localhost:8000
    """
    And an extension file "acme_page_object_extension.php" contains:
    """
    <?php

    use Behat\Mink\Mink;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
    use SensioLabs\Behat\PageObjectExtension\PageObject\InlineElement;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
    use SensioLabs\Behat\PageObjectExtension\PageObject\Factory as BaseFactory;

    class AcmeFactory implements BaseFactory
    {
        /**
         * @var Mink
         */
        private $mink = null;

        /**
         * @var array
         */
        private $pageParameters = array();

        /**
         * @var Mink  $mink
         * @var array $pageParameters
         */
        public function __construct(Mink $mink, array $pageParameters)
        {
            $this->mink = $mink;
            $this->pageParameters = $pageParameters;
        }

        /**
         * @param string $name
         *
         * @return Page
         */
        public function createPage($name)
        {
            return new $name($this->mink->getSession(), $this, $this->pageParameters);
        }

        /**
         * @param string $name
         *
         * @return Element
         */
        public function createElement($name)
        {
            return new $name($this->mink->getSession(), $this);
        }

        /**
         * @param array|string $selector
         * @param null|string  $name
         *
         * @return InlineElement
         */
        public function createInlineElement($selector, $name = null)
        {
            return new InlineElement($selector, $this->mink->getSession(), $this);
        }

        /**
         * @param string $pageObjectClass
         *
         * @return PageObject
         */
        public function create($pageObjectClass)
        {
            return new $class($this->mink->getSession(), $this, $this->pageParameters);
        }
    }

    use Behat\Testwork\ServiceContainer\Extension as TestworkExtension;
    use Behat\Testwork\ServiceContainer\ExtensionManager;
    use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
    use Symfony\Component\Config\FileLocator;
    use Symfony\Component\DependencyInjection\ContainerBuilder;
    use Symfony\Component\DependencyInjection\Definition;
    use Symfony\Component\DependencyInjection\Reference;

    class AcmePageObjectExtension implements TestworkExtension
    {
        /**
         * {@inheritdoc}
         */
        public function getConfigKey()
        {
            return 'acme_page_object';
        }

        /**
         * {@inheritdoc}
         */
        public function initialize(ExtensionManager $extensionManager)
        {
        }

        /**
         * {@inheritdoc}
         */
        public function configure(ArrayNodeDefinition $builder)
        {
        }

        /**
         * {@inheritdoc}
         */
        public function load(ContainerBuilder $container, array $config)
        {
            $definition = new Definition('AcmeFactory');
            $definition->setArguments(array(new Reference('mink'), '%mink.parameters%'));
            $container->setDefinition('acme.page_object.factory', $definition);
        }

        /**
         * {@inheritdoc}
         */
        public function process(ContainerBuilder $container)
        {
        }
    }

    return new AcmePageObjectExtension();
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
            $this->getPage('Page\Homepage')->open();
        }

        /**
         * @Then /^I should not see the tabs$/
         */
        public function iShouldSeeTheTabs()
        {
            if ($this->getPage('Page\Homepage')->hasTabs()) {
                throw new \LogicException('Tabs are visible');
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
         * @var string $path
         */
        protected $path = '/';

        public function hasTabs()
        {
            return $this->hasElement('Page\Element\SearchResultsNavigation');
        }
    }
    """
    And a page object file "features/bootstrap/Page/Element/SearchResultsNavigation.php" contains:
    """
    <?php

    namespace Page\Element;

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
