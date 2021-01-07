<?php

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

class BehatRunnerContext implements Context
{
    /**
     * @var string|null
     */
    private $workingDir;

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

        $this->process = new Process([
            $this->findPhpBinary(),
            BEHAT_BIN_PATH,
            '--format-settings={"timer": false}',
            '--format=progress'
        ], $this->workingDir);
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
  suites:
    default:
      contexts: [SearchContext]
  extensions:
    SensioLabs\Behat\PageObjectExtension: ~
    Behat\MinkExtension:
      goutte: ~
      base_url: http://localhost:8000
CONFIG;

        $this->givenBehatConfiguration(new PyStringNode(explode("\n", $config), 0));
    }

    /**
     * @Given /^a behat configuration:$/
     */
    public function givenBehatConfiguration(PyStringNode $content)
    {
        $this->getFilesystem()->dumpFile($this->workingDir.'/behat.yml', $content->getRaw());
    }

    /**
     * @Given /^an? (?:|context |page object |feature |.*)file "(?P<fileName>[^"]*)" contains:$/
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
        $this->process->start();
        $this->process->wait();
    }

    /**
     * @Then /^it should pass$/
     */
    public function itShouldPass()
    {
        if ($this->process->getExitCode() !== 0) {
            echo $this->getOutput();

            throw new \RuntimeException(sprintf('Expected a success but got a non-zero exit code: %d.', $this->process->getExitCode()));
        }

        if (preg_match('/PHP Warning: /', $this->process->getErrorOutput())) {
            throw new \RuntimeException("Did not expect a PHP Warning in the output: `%s`.", $this->process->getErrorOutput());
        }

        if (preg_match('/PHP Notice: /', $this->process->getErrorOutput())) {
            throw new \RuntimeException("Did not expect a PHP Notice in the output: `%s`.", $this->process->getErrorOutput());
        }
    }

    public function givenBehatProject($path)
    {
        $this->getFilesystem()->mirror($path, $this->workingDir);
    }

    /**
     * @param string $path
     *
     * @return Finder
     */
    public function listWorkingDir($path)
    {
        return Finder::create()
            ->files()
            ->name('*.php')
            ->in($this->workingDir.$path);
    }

    /**
     * @Then /^it should fail$/
     */
    public function itShouldFail()
    {
        if ($this->process->getExitCode() === 0) {
            echo $this->getOutput();

            throw new \RuntimeException('Expected a failure but got a zero exit code.');
        }
    }

    /**
     * @Then /^it should pass with:$/
     */
    public function itShouldPassWith(PyStringNode $expectedOutput)
    {
        $this->itShouldPass();

        if (!preg_match('/'.preg_quote($expectedOutput, '/').'/sm', $this->getOutput())) {
            throw new \RuntimeException(sprintf("Expected:\n`%s`\nbut found:\n%s.", $expectedOutput, $this->getOutput()));
        }
    }

    /**
     * @Then /^it should fail with:$/
     */
    public function itShouldFailWith(PyStringNode $expectedOutput)
    {
        $this->itShouldFail();

        if (!preg_match('/'.preg_quote($expectedOutput, '/').'/sm', $this->getOutput())) {
            throw new \RuntimeException(sprintf("Expected:\n`%s`\nbut found:\n%s.", $expectedOutput, $this->getOutput()));
        }
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
