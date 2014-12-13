Installation
============

This extension requires:

* Behat 3.0+
* Behat/MinkExtension 2.0@dev+

Through Composer
----------------

The easiest way to keep your suite updated is to use
`Composer <http://getcomposer.org>`_:

1. Define the dependencies in your `composer.json`:

    .. code-block:: js

        {
            "require": {
                "php": ">=5.4.0",
                "behat/behat": "~3.0",
                "behat/mink-extension": "~2.0@dev",
                "behat/mink-goutte-driver": "*"
            },
            "require-dev": {
                ...

                "sensiolabs/behat-page-object-extension": "~2.0"
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
            SensioLabs\Behat\PageObjectExtension: ~
            Behat\MinkExtension:
              base_url: http://environment-url.local/
              sessions:
                default:
                  goutte: ~

Through PHAR
------------

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

