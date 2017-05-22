<?php

namespace spec\SensioLabs\Behat\PageObjectExtension\Context\Argument;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SensioLabs\Behat\PageObjectExtension\PageObject\Element;
use SensioLabs\Behat\PageObjectExtension\PageObject\Factory;
use SensioLabs\Behat\PageObjectExtension\PageObject\Page;

class MyPage extends Page
{
}

class MyElement extends Element
{
}

class PageObjectArgumentResolverSpec extends ObjectBehavior
{
    function let(Factory $factory, \ReflectionClass $class, \ReflectionMethod $constructor)
    {
        $this->beConstructedWith($factory);

        $class->getConstructor()->willReturn($constructor);
    }

    function it_is_an_argument_resolver()
    {
        $this->shouldHaveType('Behat\Behat\Context\Argument\ArgumentResolver');
    }

    function it_directly_returns_arguments_if_a_class_does_not_have_a_constructor(\ReflectionClass $class, \ReflectionParameter $parameter)
    {
        $class->getConstructor()->willReturn(null);

        $this->resolveArguments($class, array($parameter))->shouldReturn(array($parameter));
    }

    function it_resolves_an_argument_if_it_is_a_page_type(Factory $factory, \ReflectionClass $class, \ReflectionMethod $constructor, \ReflectionParameter $anyParameter, \ReflectionParameter $pageParameter, \ReflectionClass $pageParameterClass, MyPage $page)
    {
        $constructor->getParameters()->willReturn(array($anyParameter, $pageParameter));
        $pageParameter->getClass()->willReturn($pageParameterClass);
        $pageParameterClass->getName()->willReturn('spec\SensioLabs\Behat\PageObjectExtension\Context\Argument\MyPage');

        $factory->create('spec\SensioLabs\Behat\PageObjectExtension\Context\Argument\MyPage')->willReturn($page);

        $this->resolveArguments($class, array())->shouldReturn(array(1 => $page));
    }

    function it_resolves_an_argument_if_it_is_an_element_type(Factory $factory, \ReflectionClass $class, \ReflectionMethod $constructor, \ReflectionParameter $anyParameter, \ReflectionParameter $elementParameter, \ReflectionClass $elementParameterClass, MyElement $element)
    {
        $constructor->getParameters()->willReturn(array($anyParameter, $elementParameter));
        $elementParameter->getClass()->willReturn($elementParameterClass);
        $elementParameterClass->getName()->willReturn('spec\SensioLabs\Behat\PageObjectExtension\Context\Argument\MyElement');

        $factory->create('spec\SensioLabs\Behat\PageObjectExtension\Context\Argument\MyElement')->willReturn($element);

        $this->resolveArguments($class, array())->shouldReturn(array(1 => $element));
    }
}
