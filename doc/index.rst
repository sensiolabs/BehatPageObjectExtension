Page Object Extension
=====================

This Behat extension provides tools for implementing page object pattern.

Page object pattern is a way of keeping your context files clean
by separating UI knowledge from the actions and assertions.
Read more on the page object pattern on the
`Selenium wiki <https://code.google.com/p/selenium/wiki/PageObjects>`_.

Installation
------------

This extension requires:

* Behat 2.4+

Through PHAR
~~~~~~~~~~~~

First, download phar archives:

* `behat.phar <http://behat.org/downloads/behat.phar>`_ - Behat itself
* `page_object_extension.phar <http://behat.org/downloads/page_object_extension.phar>`_
  - page object extension

After downloading and placing ``*.phar`` into project directory, you need to
activate ``BehatPageObjectExtension`` in your ``behat.yml``:

    .. code-block:: yaml

        default:
          # ...
          extensions:
            page_object_extension.phar: ~


Through Composer
~~~~~~~~~~~~~~~~

The easiest way to keep your suite updated is to use
`Composer <http://getcomposer.org>`_:

1. Define the dependencies in your `composer.json`:

    .. code-block:: js

        {
            "require": {
                ...

                "sensiolabs/behat-page-object-extension": "*"
            }
        }

2. Install/update your vendors:

    .. code-block:: bash

        $ curl http://getcomposer.org/installer | php
        $ php composer.phar install

3. Activate the extension in your ``behat.yml``:

    .. code-block:: yaml

        default:
          # ...
          extensions:
            SensioLabs\Behat\PageObjectExtension\Extension: ~

Page objects
------------

**Page object** encapsulates all the dirty details of an user interface.
Instead of messing with the page internals in our context files, we'd
rather ask a page object to do this for us:

    .. code-block:: php

        <?php

        /**
         * @Given /^(?:|I )change my password$/
         */
        public function iChangeMyPassword()
        {
            // $page = get page...
            $page->login('kuba', '123123')
               ->changePassword('abcabc')
               ->logout();
        }

Page objects hide the UI and expose clean services (like login or
changePassword), which can be used in the context classes.
On one side, page objects are facing the developer by providing him a clean
interface to interact with the pages. On the other side, they're facing the HTML,
being the only thing that has knowledge about a structure of a page.

The idea is we end up with much cleaner context classes and avoid duplication.
Since page objects group similar concepts together, they are easier to maintain.
For example, instead of having a concept of a login form in multiple contexts,
we'd only store it in one page object.

Creating a page object class
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To create a new page object extend the
``SensioLabs\Behat\PageObjectExtension\PageObject\Page`` class:

    .. code-block:: php

        <?php

        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class Homepage extends Page
        {
        }

Instantiating a page object
~~~~~~~~~~~~~~~~~~~~~~~~~~~

Pages are created with a built in factory. The easiest way to use them in your
context is to call ``getPage`` provided by the
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
                $this->getPage('Homepage')->search($keywords):
            }
        }

    .. note::

        Alternatively you could implement the
        ``SensioLabs\Behat\PageObjectExtension\Context\PageObjectAwareInterface``.

Page factory finds a corresponding class by the passed name:

* *"Homepage"* becomes a *"Homepage"* class
* *"Article list"* becomes an *"ArticleList"* class
* *"My awesome page"* becomes a *"MyAwesomePage"* class

    .. note::

        In future you'll be able to overload a factory to provide your own way
        of mapping page names to page object classes.

Opening a page
~~~~~~~~~~~~~~

Page can be opened by calling the ``open()`` method:

    .. code-block:: php

        <?php

        use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

        class SearchContext extends PageObjectContext
        {
            /**
             * @Given /^(?:|I )visited (?:|the )(?P<pageName>.*?)$/
             */
            public function iVisitedThePage($pageName)
            {
                $this->getPage($pageName)->open();
            }
        }

However, to be able to do this we have to provide a ``$path`` property:

    .. code-block:: php

        <?php

        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class Homepage extends Page
        {
            /**
             * @var string $path
             */
            protected $path = '/';
        }

    .. note::

        ``$path`` represents an URL of your page. You can ommit the ``$path``
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
  It is up to you to implement the logic here. The method should throw an exception
  in case URLs don't match.
* ``verifyPage()`` - verifies if the page content matches the expected content.
  It is up to you to implement the logic here. The method should throw an exception
  in case the content expected to be present on the page is not there.

Implementing page objects
~~~~~~~~~~~~~~~~~~~~~~~~~

Page is an instance of a Mink
`DocumentElement <http://mink.behat.org/api/behat/mink/element/documentelement.html>`_.
This means that instead of accessing ``Mink`` or ``Session`` objects, we can take
advantage of existing `Mink <http://mink.behat.org/>`_ Element methods:

    .. code-block:: php

        <?php

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
                    throw new ElementNotFoundException($this->getSession(), 'form', 'css', 'form#search');
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

Refrence the official `Mink API documentation <http://mink.behat.org/api/>`_ for
a full list of available methods:

* `DocumentElement <http://mink.behat.org/api/behat/mink/element/documentelement.html>`_
* `TraversableElement <http://mink.behat.org/api/behat/mink/element/traversableelement.html>`_
* `Element <http://mink.behat.org/api/behat/mink/element/element.html>`_

Note that when using page objects, the context files are only responsible for calling
methods on the page objects and making assertions. It's important to make this
separation and avoid assertions in the page objects in general.

Page objects should either return other page objects or provide ways to access
attributes of a page (like a title).

Inline elements
~~~~~~~~~~~~~~~

Page object doesn't have to relate to a whole page. It could also correspond to
some part of it - an element. Elements are page objects representing a section
of a page.

The simplest way to use elements is to define them inline in the page class:

    .. code-block:: php

        <?php

        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class Homepage extends Page
        {
            // ...

            protected $elements = array(
                'Search form' => array('css' => 'form#search'),
                'Navigation' => array('css' => '.header div.navigation'),
                'Article list' => array('xpath' => '//*[contains(@class, "content")]//ul[contains(@class, "articles")]')
            );

            /**
             * @param string $keywords
             *
             * @return Page
             */
            public function search($keywords)
            {
                $searchForm = $this->getElement('Search form');
                $searchForm->fillField('q', $keywords);
                $searchForm->pressButton('Google Search');

                return $this->getPage('Search results');
            }
        }

The advantage of this approach is that all the important page elements
are defined in one place and we can reference them from multiple methods.

Custom elements
~~~~~~~~~~~~~~~

In case of a very complex page, the page class might grow too big and become
hard to maintain. In such scenarios one option is to extract part of the logic
into a dedicated element class.

To create an element we need to extend the
``SensioLabs\Behat\PageObjectExtension\PageObject\Element`` class.
Here's a previous search example modeled as an element:


    .. code-block:: php

        <?php

        use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class SearchForm extends Element
        {
            /**
             * @var array $selector
             */
            protected $selector = array('css' => '.content form#search');

            /**
             * @param string $keywords
             *
             * @return Page
             */
            public function search($keywords)
            {
                $this->fillField('q', $keywords);
                $this->pressButton('Google Search');

                return $this->getPage('Search results');
            }
        }

Definining the ``$selector`` property is optional but recommended. When defined,
it will limit all the operations on the page to the area within the selector.
Any selector supported by Mink can be used here.

Accessing custom elements is much like accessing inline ones:

    .. code-block:: php

        <?php

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
                return $this->getElement('Search form')->search($keywords);
            }
        }

    .. note::

        Page factory takes care of creating custom elements and their class names
        follow the same rules as Page class names.

Element is an instance of a
`NodeElement <http://mink.behat.org/api/behat/mink/element/nodeelement.html>`_,
so similarly to pages, we can take advantage of existing `Mink <http://mink.behat.org/>`_
Element methods. Main difference is we have more methods relating to the single
``NodeElement``. Refrence the official `Mink API documentation <http://mink.behat.org/api/>`_ for
a full list of available methods:

* `NodeElement <http://mink.behat.org/api/behat/mink/element/nodeelement.html>`_
* `TraversableElement <http://mink.behat.org/api/behat/mink/element/traversableelement.html>`_
* `Element <http://mink.behat.org/api/behat/mink/element/element.html>`_

Writing assertions
~~~~~~~~~~~~~~~~~~

Page objects are our interface to the web pages. We still need context files
though, not only to call the page objects, but also to verify expectations.

Traditionally we'd want to throw exceptions if expectations are not met.
The difference is we'd ask a page object to provide needed page details
instead of retrieving them ourselves in the context file:

    .. code-block:: php

        class ConferenceContext extends PageObjectContext
        {
            /**
             * @Then /^(?:|I )should not be able to enrol to (?:|the )"(?P<conferenceName>[^"]*)" conference$/
             */
            public function iShouldNotBeAbleToEnrolToTheConference($conferenceName)
            {
                $page = $this->getPage('Conference list');

                if ($page->hasEnrolmentButtonFor($conferenceName)) {
                    $message = sprintf('Did not expect to find an enrollment button for the "%s" conference.', $conferenceName);

                    throw new \LogicException($message);
                }
            }
         }

Our page object could look like the following:

    .. code-block:: php

        class ConferenceList extends Page
        {
            public function hasEnrolmentButtonFor($conferenceName)
            {
                $conferenceSlug = str_replace(' ', '-', strtolower($conferenceName));
                $button = $this->find('css', sprintf('#enrol-%s', $conferenceSlug));

                return !is_null($button);
            }
         }

We could go one step fruther in making our life easier by using phpspec
matchers available through the
`expect() helper <https://github.com/BossaConsulting/phpspec2-expect>`_:

    .. code-block:: php

        /**
         * @Then /^(?:|I )should not be able to enrol to (?:|the )"(?P<conferenceName>[^"]*)" conference$/
         */
        public function iShouldNotBeAbleToEnrolToTheConference($conferenceName)
        {
            expect($this->getPage('Conference list'))->notToHaveEnrolmentButtonFor($conference);
        }

To use the `expect() helper <https://github.com/BossaConsulting/phpspec2-expect>`_,
we need to install it first. Best way to do this is by adding it to the
``composer.json``:

    .. code-block:: js

        "require-dev": {
            "bossa/phpspec2-expect": "dev-master"
        }

Configuration options
---------------------

If you use namespaces with Behat, we'll try to guess the location
of your page objects. The convention is to store pages in the ``Page``
directory located in the same place where your context files are.
Elements should go into additional ``Element`` subdirectory.

Defaults can be simply changed in the ``behat.yml`` file:

    .. code-block:: yaml

        default:
          extensions:
            SensioLabs\Behat\PageObjectExtension\Extension:
              namespaces:
                page: Acme\Features\Context\Page
                element: Acme\Features\Context\Page\Element

