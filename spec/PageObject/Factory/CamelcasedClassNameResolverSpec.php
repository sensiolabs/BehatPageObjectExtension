<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\PageObject\Factory;

use PhpSpec\ObjectBehavior;

require_once __DIR__.'/Fixtures/ArticleList.php';
require_once __DIR__.'/Fixtures/NamespacedArticleList.php';
require_once __DIR__.'/Fixtures/Element/SearchBox.php';
require_once __DIR__.'/Fixtures/Element/NamespacedSearchBox.php';

class CamelcasedClassNameResolverSpec extends ObjectBehavior
{
    function it_is_a_class_name_resolver()
    {
        $this->shouldHaveType('SensioLabs\Behat\PageObjectExtension\PageObject\Factory\ClassNameResolver');
    }

    function it_resolves_a_page_class_from_the_global_namespace()
    {
        $this->resolvePage('Article list')->shouldReturn('\\ArticleList');
    }

    function it_resolves_a_page_class_from_configured_namespaces()
    {
        $this->beConstructedWith(array('spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures'));

        $this->resolvePage('Namespaced article list')->shouldReturn('\\spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures\\NamespacedArticleList');
    }

    function it_resolves_a_page_class_from_a_namespace_with_trailing_namespace_separator()
    {
        $this->beConstructedWith(array('\\spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures\\'));

        $this->resolvePage('Namespaced article list')->shouldReturn('\\spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures\\NamespacedArticleList');
    }

    function it_resolves_a_page_class_given_as_fqcn()
    {
        $this->beConstructedWith(array('\\Foo', '\\Page'));

        $pageFQCN = '\\spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures\\NamespacedArticleList';
        $this->resolvePage($pageFQCN)->shouldReturn($pageFQCN);
    }

    function it_tries_all_the_namespaces_before_throwing_an_exception()
    {
        $this->beConstructedWith(array(
            'spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory',
            'spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures'
        ));

        $this->resolvePage('Namespaced article list')->shouldReturn('\\spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures\\NamespacedArticleList');
    }

    function it_throws_an_exception_if_page_class_does_not_exist()
    {
        $this->beConstructedWith(array('\\', '\\Page'));

        $expectedMessage = 'Could not find a class for the "News list" page. None of the configured namespaces worked: "\\NewsList, \\Page\\NewsList"';

        $this->shouldThrow(new \InvalidArgumentException($expectedMessage))
            ->duringResolvePage('News list');
    }

    function it_resolves_an_element_class_from_the_global_namespace()
    {
        $this->resolveElement('Search box')->shouldReturn('\\SearchBox');
    }

    function it_resolves_an_element_class_from_configured_namespaces()
    {
        $this->beConstructedWith(array(), array('spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures\\Element'));

        $this->resolveElement('Namespaced search box')->shouldReturn('\\spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures\\Element\\NamespacedSearchBox');
    }

    function it_throws_an_exception_if_element_class_does_not_exist()
    {
        $this->beConstructedWith(array(), array('\\', '\\Element'));

        $expectedMessage = 'Could not find a class for the "Recent searches" element. None of the configured namespaces worked: "\\RecentSearches, \\Element\\RecentSearches"';

        $this->shouldThrow(new \InvalidArgumentException($expectedMessage))
            ->duringResolveElement('Recent searches');
    }

    function it_resolves_an_element_class_given_as_fqcn()
    {
        $this->beConstructedWith(array('\\Foo', '\\Element'));

        $elementFQCN = '\\spec\\SensioLabs\\Behat\\PageObjectExtension\\PageObject\\Factory\\Fixtures\\Element\\NamespacedSearchBox';
        $this->resolvePage($elementFQCN)->shouldReturn($elementFQCN);
    }
}
