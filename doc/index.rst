Page Object Extension
=====================

Behat extension providing tools to implement page object pattern.

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

The easiest way to keep your suite updated is to use `Composer <http://getcomposer.org>`_:

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

Using elements
--------------

Configuration options
---------------------

