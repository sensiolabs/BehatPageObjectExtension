Working with page objects
=========================

Creating a page object class
----------------------------

To create a new page object extend the
``SensioLabs\Behat\PageObjectExtension\PageObject\Page`` class:

    .. code-block:: php

        <?php

        namespace Page;

        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class Homepage extends Page
        {
        }

Instantiating a page object
---------------------------

Injecting page objects into a context file
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Page objects will be injected directly into a context file if they're defined as constructor arguments
with a type hint:

    .. code-block:: php

        <?php

        use Behat\Behat\Context\Context;
        use Page\Homepage;
        use Page\Element\Navigation;

        class SearchContext implements Context
        {
            private $homepage;
            private $navigation;

            public function __construct(Homepage $homepage, Navigation $navigation)
            {
                $this->homepage = $homepage;
                $this->navigation = $navigation;
            }

            /**
             * @Given /^(?:|I )visited homepage$/
             */
            public function iVisitedThePage()
            {
                $this->homepage->open();
            }
        }

Using the page object factory
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Pages are created with a built in factory. One of the ways to use them in your
context is to call the ``getPage`` method provided by the
``SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext``:

    .. code-block:: php

        <?php

        use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

        class SearchContext extends PageObjectContext
        {
            /**
             * @Given /^(?:|I )search for (?P<keywords>.*?)$/
             */
            public function iSearchFor($keywords)
            {
                $this->getPage('Homepage')->search($keywords);
            }
        }

    .. note::

        Alternatively you could implement the
        ``SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface``.

Page factory finds a corresponding class by the passed name:

* *"Homepage"* becomes a *"Homepage"* class
* *"Article list"* becomes an *"ArticleList"* class
* *"My awesome page"* becomes a *"MyAwesomePage"* class

From version 2.1 it is possibile to use ``getPage()`` method with page FQCN as follows

    .. code-block:: php

        <?php

        use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;
        use Page\Homepage;

        class SearchContext extends PageObjectContext
        {
            /**
             * @Given /^(?:|I )search for (?P<keywords>.*?)$/
             */
            public function iSearchFor($keywords)
            {
                // For PHP >= 5.5.0
                $this->getPage(Homepage::class)->search($keywords);
                // For PHP < 5.5.0
                $this->getPage('Page\\Homepage')->search($keywords);
            }
        }

If you choose FQCN strategy, you can organize your page directories freely as you are not bounded to page namespace
(see :doc:`../guide/configuration`)

    .. note::
        You can choose between "CamelCase" strategy and "FQCN" strategy. We recommend to keep a consistent strategy for
        the factory but there is not any constraint: both strategies can work togheter with their own rules.

    .. note::

        It is possible to implement your own way of mapping a page name to
        an appropriate page object with a :doc:`custom factory </cookbooks/custom_factory>`.

Opening a page
--------------

Page can be opened by calling the ``open()`` method:

    .. code-block:: php

        <?php

        use Behat\Behat\Context\Context;
        use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

        class SearchContext implements Context
        {
            private $homepage;
            private $navigation;

            public function __construct(Homepage $homepage, Navigation $navigation)
            {
                $this->homepage = $homepage;
                $this->navigation = $navigation;
            }

            /**
             * @Given /^(?:|I )visited (?:|the )(?P<pageName>.*?)$/
             */
            public function iVisitedThePage($pageName)
            {
                if (!isset($this->$pageName)) {
                    throw new \RuntimeException(sprintf('Unrecognised page: "%s".', $pageName));
                }

                $this->$pageName->open();
            }
        }

However, to be able to do this we have to provide a ``$path`` property:

    .. code-block:: php

        <?php

        namespace Page;

        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class Homepage extends Page
        {
            /**
             * @var string $path
             */
            protected $path = '/';
        }

    .. note::

        ``$path`` represents an URL of your page. You can omit the ``$path``
        if your page object is only returned from other pages and you're not
        planning on opening it directly. ``$path`` is only used if you call
        ``open()`` on the page.

Path can also be parametrised:

    .. code-block:: php

            protected $path = '/employees/{employeeId}/messages';

Any parameters should be given to the ``open()`` method:

    .. code-block:: php

            $this->getPage($pageName)->open(array('employeeId' => 13));

It's also possible to check if a given page is opened with ``isOpen()`` method:

    .. code-block:: php

        $isOpen = $this->getPage($pageName)->isOpen(array('employeeId' => 13));

Both ``open()`` and ``isOpen()`` run the same verifications, which can be overriden:

* ``verifyResponse()`` - verifies if the response was successful.
  It only works for drivers which support getting a response status code.
* ``verifyUrl()`` - verifies if the current URL matches the expected one.
  The default implementation only checks if a page url is exactly the same
  as the current url. Override this method to implement your custom matching
  logic. The method should throw an exception in case URLs don't match.
* ``verifyPage()`` - verifies if the page content matches the expected content.
  It is up to you to implement the logic here. The method should throw an exception
  in case the content expected to be present on the page is not there.

Implementing page objects
-------------------------

Page is an instance of a Mink
`DocumentElement <http://mink.behat.org/api/behat/mink/element/documentelement.html>`_.
This means that instead of accessing ``Mink`` or ``Session`` objects, we can take
advantage of existing `Mink <http://mink.behat.org/>`_ Element methods:

    .. code-block:: php

        <?php

        namespace Page;

        use Behat\Mink\Exception\ElementNotFoundException;
        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class Homepage extends Page
        {
            // ...

            /**
             * @param string $keywords
             *
             * @return Page
             */
            public function search($keywords)
            {
                $searchForm = $this->find('css', 'form#search');

                if (!$searchForm) {
                    throw new ElementNotFoundException($this->getDriver(), 'form', 'css', 'form#search');
                }

                $searchForm->fillField('q', $keywords);
                $searchForm->pressButton('Google Search');

                return $this->getPage('Search results');
            }
        }

Notice that after clicking the *Search* button we'll be redirected to a search results
page. Our method reflects this intent and returns another page by creating it with
a ``getPage()`` helper method first.
Pages are created with the same factory which is used in the context files.

Reference the official `Mink API documentation <http://mink.behat.org/api/>`_ for
a full list of available methods:

* `DocumentElement <http://mink.behat.org/api/behat/mink/element/documentelement.html>`_
* `TraversableElement <http://mink.behat.org/api/behat/mink/element/traversableelement.html>`_
* `Element <http://mink.behat.org/api/behat/mink/element/element.html>`_

Note that when using page objects, the context files are only responsible for calling
methods on the page objects and making assertions. It's important to make this
separation and avoid assertions in the page objects in general.

Page objects should either return other page objects or provide ways to access
attributes of a page (like a title).
