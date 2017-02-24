Working with page object elements
=================================

Page object doesn't have to relate to a whole page. It could also correspond to
some part of it - an element. Elements are page objects representing a section
of a page.

Inline elements
---------------

The simplest way to use elements is to define them inline in the page class:

    .. code-block:: php

        <?php

        namespace Page;

        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class Homepage extends Page
        {
            // ...

            protected $elements = array(
                'Search form' => 'form#search',
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

The `$elements` array should be a list of selectors indexed by element
names. The selector can be either a string or an array. If it's a string,
a css selector is assumed. The key of an array is used otherwise.

The difference between the `getElement()` method and Mink's `find()`,
is that the later might return `null`, while the first will throw
and exception when an element is not found on the page.

Custom elements
---------------

In case of a very complex page, the page class might grow too big and become
hard to maintain. In such scenarios one option is to extract part of the logic
into a dedicated element class.

To create an element we need to extend the
``SensioLabs\Behat\PageObjectExtension\PageObject\Element`` class.
Here's a previous search example modeled as an element:


    .. code-block:: php

        <?php

        namespace Page\Element;

        use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
        use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

        class SearchForm extends Element
        {
            /**
             * @var array|string $selector
             */
            protected $selector = '.content form#search';

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

Defining the ``$selector`` property is optional but recommended. When defined,
it will limit all the operations on the page to the area within the selector.
Any selector supported by Mink can be used here.

Similarly to the inline elements, the selector can be either a string or an array.
If it's a string, a css selector is assumed. The key of an array is used otherwise.

Accessing custom elements is much like accessing inline ones:

    .. code-block:: php

        <?php

        namespace Page;

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
                // or (for PHP >= 5.5.0) return $this->getElement(SearchForm::class)->search($keywords);
                // or (for PHP < 5.5.0) return $this->getElement('Page\\Homepage')->search($keywords);
            }
        }

    .. note::

        Page factory takes care of creating custom elements and their class names
        follow the same rules as Page class names.

Element is an instance of a
`NodeElement <http://mink.behat.org/api/behat/mink/element/nodeelement.html>`_,
so similarly to pages, we can take advantage of existing `Mink <http://mink.behat.org/>`_
Element methods. Main difference is we have more methods relating to the single
``NodeElement``. Reference the official `Mink API documentation <http://mink.behat.org/api/>`_ for
a full list of available methods:

* `NodeElement <http://mink.behat.org/api/behat/mink/element/nodeelement.html>`_
* `TraversableElement <http://mink.behat.org/api/behat/mink/element/traversableelement.html>`_
* `Element <http://mink.behat.org/api/behat/mink/element/element.html>`_

