<?php

use Behat\Behat\Context\Context;
use Symfony\Component\Process\Process;

class PhpServerContext implements Context
{
    /**
     * @var Process
     */
    private static $phpServer;

    /**
     * @BeforeSuite
     */
    public static function startPhpServer()
    {
        self::$phpServer = new Process('php -S localhost:8000 features/application/index.php');
        self::$phpServer->start();

        sleep(1);
    }

    /**
     * @AfterSuite
     */
    public static function stopPhpServer()
    {
        self::$phpServer->stop();
    }
}
