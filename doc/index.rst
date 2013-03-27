Page Object Extension
=====================

Behat extension providing tools to implement page object pattern.

**Page object** encapsulates all the dirty details of an user interface.
Instead of messing with the low level page details in our context files, we'd
ask a page object to do this for us:

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
        [Selenium wiki](https://code.google.com/p/selenium/wiki/PageObjects).

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

Using elements
--------------

Configuration options
---------------------

