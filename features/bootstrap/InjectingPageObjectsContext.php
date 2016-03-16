<?php

use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Context\Context;

final class InjectingPageObjectsContext implements Context
{
    /**
     * @var BehatRunnerContext|null
     */
    private $behatRunnerContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $this->behatRunnerContext = $scope->getEnvironment()->getContext('BehatRunnerContext');
    }

    /**
     * @Given a feature with a context file that uses page objects
     */
    public function aFeatureWithAContextFileThatUsesPageObjects()
    {
        $this->behatRunnerContext->givenBehatProject('features/fixtures/default/');
    }

    /**
     * @Then the proxies should be generated in the :path directory
     */
    public function theProxiesShouldBeGeneratedInTheDirectory($path)
    {
        $filesCount = $this->behatRunnerContext->listWorkingDir($path)->count();

        if ($filesCount < 1) {
            throw new \LogicException('Expected at least one proxy to be generated but found none.');
        }
    }
}
