Creating a custom factory
=========================

To implement a custom page object factory the
``SensioLabs\Behat\PageObjectExtension\PageObject\Factory``
needs to be implemented, and registered as a service within your extension.

Id of the service has to be then configured in the ``behat.yml``:

    .. code-block:: yaml

        default:
          extensions:
            SensioLabs\Behat\PageObjectExtension:
              factory: acme.page_object.factory

Custom class name resolver with the default factory
---------------------------------------------------

In most cases the default page object factory should meet our needs,
and what we'd like to overload is the class name resolver.

Class name resolver is used by the default factory to actually convert
the page object name to a class namespace.

To implement a custom class name resolver the
``SensioLabs\Behat\PageObjectExtension\PageObject\Factory\ClassNameResolver``
needs to be implemented and registered as a service within your extension.

Id of the service has to be then configured in the ``behat.yml``:

    .. code-block:: yaml

        default:
          extensions:
            SensioLabs\Behat\PageObjectExtension:
              factory:
                id: ~ #optional
                class_name_resolver: acme.page_object.class_name_resolver
