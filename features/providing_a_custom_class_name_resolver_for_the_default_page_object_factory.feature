Feature: Providing a custom class name resolver for the default page object factory
  In order to customise the way page object class names are resolved
  As an Experienced Developer
  I need to provide my own implementation of the class name resolver

  Scenario: Custom class name resolver
    Given a behat configuration:
    """
    default:
      suites:
        default:
          contexts: [SearchContext]
      extensions:
        SensioLabs\Behat\PageObjectExtension:
          factory:
            id: ~
            class_name_resolver: acme.page_object.class_name_resolver
        acme_page_object_extension.php: ~
        Behat\MinkExtension:
          goutte: ~
          base_url: http://localhost:8000
    """
    And an extension file "acme_page_object_extension.php" contains:
    """
    <?php

    use SensioLabs\Behat\PageObjectExtension\PageObject\Factory\ClassNameResolver;

    class AcmeClassNameResolver implements ClassNameResolver
    {
        /**
         * @param string $name
         *
         * @return string
         */
        public function resolvePage($name)
        {
            return str_replace(array(' ', '->'), array('', '\\'), ucwords($name));
        }

        /**
         * @param string $name
         *
         * @return string
         */
        public function resolveElement($name)
        {
            return $this->resolvePage($name);
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
            $definition = new Definition('AcmeClassNameResolver');
            $container->setDefinition('acme.page_object.class_name_resolver', $definition);
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
            $this->getPage('Page -> homepage')->open();
        }

        /**
         * @Then /^I should not see the tabs$/
         */
        public function iShouldSeeTheTabs()
        {
            if ($this->getPage('Page -> homepage')->hasTabs()) {
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
            return $this->hasElement('Page -> Element -> SearchResultsNavigation');
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
