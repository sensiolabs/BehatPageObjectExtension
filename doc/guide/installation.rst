Installation
============

This extension requires:

* Behat 3.0+

Through Composer
----------------

The easiest way to keep your suite updated is to use
`Composer <http://getcomposer.org>`_.

1. Require the extension in your ``composer.json``:

    .. code-block:: bash

        $ composer require --dev sensiolabs/behat-page-object-extension:^2.0

2. Activate the extension in your ``behat.yml``:

    .. code-block:: yaml

        default:
          # ...
          extensions:
            SensioLabs\Behat\PageObjectExtension: ~


