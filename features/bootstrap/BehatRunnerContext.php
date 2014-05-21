<?php

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class BehatRunnerContext extends BehatContext
{
    /**
     * @var string|null
     */
    private $workingDir;

    /**
     * @var string|null
     */
    private $phpBin;

    /**
     * @var Process|null
     */
    private $process;

    /**
     * @BeforeScenario
     */
    public function bootstrap()
    {
        $this->workingDir = sprintf('%s/%s/', sys_get_temp_dir(), uniqid('BehatPageObjectExtension_'));
        $this->getFilesystem()->mkdir($this->workingDir, 0777);

        $this->phpBin = $this->findPhpBinary();
        $this->process = new Process(null);
    }

    /**
     * @AfterScenario
     */
    public function removeWorkDir()
    {
        $this->getFilesystem()->remove($this->workingDir);
    }

    /**
     * @Given /^I configured the page object extension$/
     */
    public function iConfiguredThePageObjectExtension()
    {
        $config = <<<CONFIG
default:
  context:
    class: SearchContext
  extensions:
    SensioLabs\Behat\PageObjectExtension\Extension: ~
    Behat\MinkExtension\Extension:
      goutte: ~
      base_url: http://localhost:8000
CONFIG;

        $this->givenBehatConfiguration(new PyStringNode($config));
    }

    /**
     * @Given /^a behat configuration:$/
     */
    public function givenBehatConfiguration(PyStringNode $content)
    {
        $this->getFilesystem()->dumpFile($this->workingDir.'/behat.yml', $content->getRaw());
    }

    /**
     * @Given /^a (?:|context |page object |feature )file "(?P<fileName>[^"]*)" contains:$/
     */
    public function aContextFileNamedWith($fileName, PyStringNode $content)
    {
        $this->getFilesystem()->dumpFile($this->workingDir.'/'.$fileName, $content->getRaw());
    }

    /**
     * @When /^I run behat$/
     */
    public function iRunBehat()
    {
        $this->process->setWorkingDirectory($this->workingDir);
        $this->process->setCommandLine(
            sprintf(
                '%s %s %s',
                $this->phpBin,
                escapeshellarg(BEHAT_BIN_PATH),
                '--no-time --format=progress'
            )
        );
        $this->process->start();
        $this->process->wait();
    }

    /**
     * @Then /^it should pass$/
     */
    public function itShouldPass()
    {
        try {
            expect($this->process->getExitCode())->toBe(0);
        } catch (\Exception $e) {
            echo $this->getOutput();

            throw $e;
        }
    }

    /**
     * @Then /^it should fail$/
     */
    public function itShouldFail()
    {
        try {
            expect($this->process->getExitCode())->notToBe(0);
        } catch (\Exception $e) {
            echo $this->getOutput();

            throw $e;
        }
    }

    /**
     * @Then /^it should pass with:$/
     */
    public function itShouldPassWith(PyStringNode $expectedOutput)
    {
        $this->itShouldPass();

        expect($this->getOutput())->toMatch('/'.preg_quote($expectedOutput, '/').'/sm');
    }

    /**
     * @Then /^it should fail with:$/
     */
    public function itShouldFailWith(PyStringNode $expectedOutput)
    {
        $this->itShouldFail();

        expect($this->getOutput())->toMatch('/'.preg_quote($expectedOutput, '/').'/sm');
    }

    /**
     * @return string
     */
    private function getOutput()
    {
        return $this->process->getErrorOutput().$this->process->getOutput();
    }

    /**
     * @return Filesystem
     */
    private function getFilesystem()
    {
        return new Filesystem();
    }

    /**
     * @return string
     * @throws RuntimeException
     */
    private function findPhpBinary()
    {
        $phpFinder = new PhpExecutableFinder();

        if (false === $php = $phpFinder->find()) {
            throw new \RuntimeException('Unable to find the PHP executable.');
        }

        return $php;
    }
}