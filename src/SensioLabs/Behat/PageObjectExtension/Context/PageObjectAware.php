<?php

namespace SensioLabs\Behat\PageObjectExtension\Context;

@trigger_error('The '.__NAMESPACE__.'\PageObjectAware interface is deprecated since version 2.0 and will be removed in 3.0. Use the argument injection instead.', E_USER_DEPRECATED);

use SensioLabs\Behat\PageObjectExtension\PageObject\Factory as PageObjectFactory;

/**
 * @deprecated in 2.0, to be removed in 3.0. Use the argument resolver instead. See http://behat-page-object-extension.readthedocs.org/en/latest/guide/working_with_page_objects.html#instantiating-a-page-object
 */
interface PageObjectAware
{
    /**
     * @param PageObjectFactory $pageObjectFactory
     *
     * @return null
     */
    public function setPageObjectFactory(PageObjectFactory $pageObjectFactory);
}
