Configuration
=============

If you use namespaces with Behat, we'll try to guess the location
of your page objects. The convention is to store pages in the ``Page``
directory located in the same place where your context files are.
Elements should go into additional ``Element`` subdirectory.

Defaults can be simply changed in the ``behat.yml`` file:

    .. code-block:: yaml

        default:
          extensions:
            SensioLabs\Behat\PageObjectExtension:
              namespaces:
                page: [Acme\Features\Context\Page, Acme\Page]
                element: [Acme\Features\Context\Page\Element, Acme\Page\Element]
              factory:
                id: acme.page_object.factory
                page_parameters:
                  base_url: http://localhost
                proxies_target_dir: /path/to/tmp/

