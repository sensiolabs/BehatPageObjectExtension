<?php

namespace SensioLabs\Behat\PageObjectExtension\PageObject\Selector;

use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidSelectorDeclarationException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class SelectorFactory implements SelectorFactoryInterface
{
    /**
     * Map of registered selectors supported by this factory
     *
     * @var ParameterBag
     */
    protected $selectorsMap;

    /**
     * Create factory
     */
    public function __construct()
    {
        $this->selectorsMap = new ParameterBag();
    }

    /**
     * Registry new type of selector
     *
     * @param string $type  Type of selector
     * @param string $class Class
     */
    public function registry($type, $class)
    {
        $this->selectorsMap->set($type, $class);
    }

    /**
     * Creates selector from given arguments.
     *
     * Possible calls:
     * - SelectorFactory::create(array('css', 'div.light'));
     * - SelectorFactory::create('css', 'div.light');
     *
     * @return SelectorInterface
     * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidSelectorDefinitionException
     */
    public function create()
    {
        $args = func_get_args();
        $argno = func_num_args();
        switch (true) {
            case ($argno == 1 && is_array($args[0])):
                return $this->createSelectorFrom($args[0]);
                break;
            case ($argno == 2 && is_string($args[0]) && is_string($args[1])):
                return $this->createSelector($args[0], $argno[1]);
                break;
            default:
                throw new InvalidSelectorDeclarationException("Invalid SelectorFactory::create call, expected array or string.");
        }
    }

    /**
     * Create selector for given parameters
     *
     * @param $type
     * @param $path
     *
     * @throws \SensioLabs\Behat\PageObjectExtension\PageObject\Exception\InvalidSelectorDeclarationException
     * @return SelectorInterface
     */
    private function createSelector($type, $path)
    {
        if (!$this->selectorsMap->has($type)) {
            throw new InvalidSelectorDeclarationException(
                sprintf('Array definition should contain only css or xpath selector elements, but definition for %s has been found.', $type)
            );
        }

        $class = $this->selectorsMap->get($type);

        return new $class($path);
    }

    /**
     * @param array $array
     *
     * @return SelectorInterface
     */
    private function createSelectorFrom(array $array)
    {
        $key = key($array);
        return $this->createSelector($key, $array[$key]);
    }
}
