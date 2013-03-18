<?php

namespace spec\SensioLabs\PageObjectExtension\Context;

use PHPSpec2\ObjectBehavior;

require_once __DIR__.'/Fixtures/ArticleList.php';
require_once __DIR__.'/Fixtures/NamespacedArticleList.php';

class PageFactory extends ObjectBehavior
{
    /**
     * @param \Behat\Mink\Session $session
     */
    function let($session)
    {
        $this->beConstructedWith($session, array('base_url' => 'http://behat.dev'));
    }

    function it_should_create_a_page()
    {
        $this->create('Article list')->shouldBeAnInstanceOf('ArticleList');
    }

    function it_should_overwrite_the_default_namespace()
    {
        foreach ($this->getNamespaces() as $namespace => $class) {
            $this->setNamespace($namespace);
            $this->create('Namespaced Article list')->shouldBeAnInstanceOf($class);
        }
    }

    private function getNamespaces()
    {
        return array(
            'spec\SensioLabs\PageObjectExtension\Context\Fixtures' => '\spec\SensioLabs\PageObjectExtension\Context\Fixtures\NamespacedArticleList',
            '\spec\SensioLabs\PageObjectExtension\Context\Fixtures' => '\spec\SensioLabs\PageObjectExtension\Context\Fixtures\NamespacedArticleList',
            'spec\SensioLabs\PageObjectExtension\Context\Fixtures\\' => '\spec\SensioLabs\PageObjectExtension\Context\Fixtures\NamespacedArticleList'
        );
    }

    function it_should_complain_if_page_object_does_not_exist()
    {
        $this->shouldThrow(new \LogicException('"Home" page not recognised'))->duringCreate('Home');
    }
}
