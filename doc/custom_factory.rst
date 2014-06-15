Custom factory
==============

To implement a custom page object factory the ``SensioLabs\Behat\PageObjectExtension\PageObject\Factory``
needs to be implemented and registered as a service within your extension.

Id of the service has to be than configured in the ``behat.yml``:

    .. code-block:: yaml

        default:
          extensions:
            SensioLabs\Behat\PageObjectExtension:
              factory: acme.page_object.factory