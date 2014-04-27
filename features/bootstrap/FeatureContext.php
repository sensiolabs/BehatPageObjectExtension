<?php

use Behat\Behat\Context\BehatContext;

class FeatureContext extends BehatContext
{
    /**
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->useContext('php_server', new PhpServerContext());
        $this->useContext('behat_runner', new BehatRunnerContext());
    }
}
