Page Object Extension
=====================

Behat extension providing tools to implement page object pattern.

**Page object** encapsulates all the dirty details of an user interface.
Instead of messing with the low level page details in our context files, we'd
ask a page object to do this for us:

    .. code-block:: php

        $page->login('kuba', '123123')
           ->changePassword('abcabc')
           ->logout();

Page objects hide the UI and expose clean services we can use in the context
classes (login, changePassword etc). On one side they're facing the developer,
by providing him a clean interface to interact with the pages. On the other side
they're facing the HTML, being the only thing that has knowledge about the
structure of a page.

This way we end up with much cleaner context classes and avoid duplication.
Since page objects group similar concepts together, they are easier to maintain.
Instead of having a concept of a login form in multiple contexts, we only store
it in one page object.

When using page objects, the context files are only responsible for calling
methods on the page objects and making assertions. It's important to make this
separation and not make assertions in the page objects in general. Page objects
should either return other page objects or provide ways to access attributes of
a page (like a title).

Page object doesn't have to represent a single page. It could also represent a
part of it. If a certain area of page exposes lots of services, it can be
modelled as a separate page object. Our extension calls it an element.

    .. note::

        Page object pattern was defined for the first time by the Selenium
        community and you can read more about it on the
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

Creating a page object
----------------------

To create a new page object extend the
``SensioLabs\Behat\PageObjectExtension\PageObject\Page`` class:

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
        plainng on opening it directly. ``$path`` is only used if you call
        ``open()`` on the page.

Pages are created with a factory. The easiest way to use them in your context
is to extend the
``SensioLabs\\Behat\\PageObjectExtension\\Context\\PageObjectContext``:

    .. code-block:: php

        <?php

        use SensioLabs\Behat\PageObjectExtension\Context\PageObjectContext;

        class SearchContext extends PageObjectContext
        {
            /**
             * @Given /^(?:|I )visited (?:|the )(?P<pageName>.*?)$/
             */
            public function iVisitedTheHomepage($pageName)
            {
                $this->getPage($pageName)->open();
            }
        }

    .. note::

        Alternatively you could implement the
        ``SensioLabs\\Behat\\PageObjectExtension\\Context\\PageObjectAwareInterface``.

Factory finds a corresponding class by the passed name:

* "Homepage" becomes a "Homepage" class
* "Article list" becomes an "ArticleList" class
* "My awesome page" becomes a "MyAwesomePage" class

This way we can map a name of a page directly to the class name.

    .. note::

        In future you'll be able to overload a factory to provide your own way
        of mapping page names to page object classes.

Page is an instance of a
`DocumentElement <http://mink.behat.org/api/behat/mink/element/documentelement.html>`_.
This means that instead of accessing Mink or Session objects, we can take
advantage of existing `Mink <http://mink.behat.org/>`_ Element's methods:

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
                $searchInput = $this->find('css', 'input#search');

                if (!$searchInput) {
                    throw new ElementNotFoundException($this->getSession(), 'input', 'css', 'input#search');
                }

                $searchInput->setValue($keywords);

                $this->pressButton('Google Search');

                return $this->getPage('Search results');
            }
        }

Notice that after clicking the *Search* button we'll be redirected to a search results
page. Our method reflects this intent and returns another page by creating it with
a ``getPage()`` helper first. Pages are created with the same factory which is used in
the context files.

Refrence the official `Mink API documentation <http://mink.behat.org/api/>`_ for
a full list of available methods:
* `DocumentElement <http://mink.behat.org/api/behat/mink/element/documentelement.html>`_
* `TraversableElement <http://mink.behat.org/api/behat/mink/element/traversableelement.html>`_
* `Element <http://mink.behat.org/api/behat/mink/element/element.html>`_

Using elements
--------------

Elements are page objects representing a section of a page. Good candidates for
an element would be a navigation or a form.

To create an element we need to extend the
``SensioLabs\Behat\PageObjectExtension\PageObject\Element`` class. Here's a
previous search example modeled as an element:


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

Definining the ``$selector`` property is optional. When defined, it will limit
all the operations on the page to the area withing the selector.
Any selector supported by Mink can be used here.

Elements are only accessible from pages:

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

Element is an instance of a
`NodeElement <http://mink.behat.org/api/behat/mink/element/nodeelement.html>`_,
so similarly to pages, we can take advantage of existing `Mink <http://mink.behat.org/>`_
Element's methods. Main difference is we have more methods relating to the single
``NodeElement``. Refrence the official `Mink API documentation <http://mink.behat.org/api/>`_ for
a full list of available methods:
* `NodeElement <http://mink.behat.org/api/behat/mink/element/nodeelement.html>`_
* `TraversableElement <http://mink.behat.org/api/behat/mink/element/traversableelement.html>`_
* `Element <http://mink.behat.org/api/behat/mink/element/element.html>`_

Configuration options
---------------------

