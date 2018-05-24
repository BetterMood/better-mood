<?php

namespace Moodle;

use Moodle\Egress\EgressException;

class PhpMinimumVersionTest extends \PHPUnit\Framework\TestCase
{
    const ERROR_MESSAGE = 'Moodle 3.4 or later requires at least PHP ' . self::PHP_MINIMUM_VERSION . ' (currently using version ' . self::LESS_THAN_PHP_MINIMUM_VERSION . ').' . PHP_EOL . 'Some servers may have multiple PHP versions installed, are you using the correct executable?' . PHP_EOL;
    const CLI_SCRIPT = true;
    const NOT_CLI_SCRIPT = false;
    const HALT_EXECUTION = true;
    const NO_HALT_EXECUTION = false;
    const PHP_MINIMUM_VERSION = '7.1.0';
    const LESS_THAN_PHP_MINIMUM_VERSION = '7.0.0';

    private static $errorStream;

    public static function setUpBeforeClass()
    {
        self::$errorStream = fopen('/dev/null', 'w');
    }

    public static function tearDownAfterClass()
    {
        fclose(self::$errorStream);
    }

    /**
     * @dataProvider getBools
     */
    public function testRequireMinimumPhpVersionDoesNothingIfPhpVersionIsRequiredMinimun($isCliScript)
    {
        $phpMinimumVersion = new PhpMinimumVersion(self::PHP_MINIMUM_VERSION, $isCliScript, self::$errorStream);
        $this->assertNull($phpMinimumVersion->requireMinimumPhpVersion());
    }

    /**
     * @dataProvider getBools
     */
    public function testMinimumPhpVersionIsMetReturnsTrueIfPhpVersionIsRequiredMinimum($isCliScript, $haltExecution)
    {
        $phpMinimumVersion = new PhpMinimumVersion(self::PHP_MINIMUM_VERSION, $isCliScript, self::$errorStream);
        $this->assertTrue($phpMinimumVersion->minimumPhpVersionIsMet($haltExecution));
    }

    /**
     * @expectedException \Moodle\Egress\EgressException
     * @dataProvider getBools
     */
    public function testRequireMinimumPhpVersionCallsExitIfPhpVersionIsLessThanRequiredMinimum($isCliScript)
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, $isCliScript, self::$errorStream);
        $phpMinimumVersion->requireMinimumPhpVersion();
    }

    /**
     * @expectedException \Moodle\Egress\EgressException
     * @dataProvider getBools
     */
    public function testPhpMinimumVersionIsMetCallsExitIfPhpVersionIsLessThanRequiredMinimumAndHaltexecutionIsTrue($isCliScript)
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, $isCliScript, self::$errorStream);
        $this->assertFalse($phpMinimumVersion->minimumPhpVersionIsMet(self::HALT_EXECUTION));
    }

    /**
     * @dataProvider getBools
     */
    public function testPhpMinimumVersionIsMetDoesNotCallExitIfPhpVersionIsLessThanRequiredMinimumAndHaltexecutionIsFalse($isCliScript)
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, $isCliScript, self::$errorStream);
        $this->assertFalse($phpMinimumVersion->minimumPhpVersionIsMet(self::NO_HALT_EXECUTION));
    }

    /**
     * @expectedException \Moodle\Egress\EgressException
     */
    public function testRequireMinimumPhpVersionOutputsMessageIfPhpVersionIsLessThanRequiredMinimumAndThisIsNotACliScript()
    {
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, self::NOT_CLI_SCRIPT, self::$errorStream);
        $this->expectOutputString(self::ERROR_MESSAGE);
        $phpMinimumVersion->requireMinimumPhpVersion();
    }

    /**
     * @expectedException \Moodle\Egress\EgressException
     */
    public function testMinimumPhpVersionIsMetOutputsMessageIfPhpVersionIsLessThanRequiredMinimumAndThisIsNotACliScriptAndHaltexecutionIsTrue()
    {
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, self::NOT_CLI_SCRIPT, self::$errorStream);
        $this->expectOutputString(self::ERROR_MESSAGE);
        $phpMinimumVersion->minimumPhpVersionIsMet(self::HALT_EXECUTION);
    }

    public function testMinimumPhpVersionIsMetDoesNotOutputMessageIfPhpVersionIsLessThanRequiredMinimumAndThisIsNotACliScriptAndHaltexecutionIsFalse()
    {
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, self::NOT_CLI_SCRIPT, self::$errorStream);
        $this->expectOutputString('');
        $phpMinimumVersion->minimumPhpVersionIsMet(self::NO_HALT_EXECUTION);
    }

    public function testRequireMinimumPhpVersionWritesMessageToErrorStreamIfPhpVersionIsLessThanRequiredMinimumAndThisIsACliScript()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, self::CLI_SCRIPT, $errorStream);

        /**
         * The call is wrapped in a try-catch because we need to check
         * the error output after the exception is thrown, but
         * PHPUnit's @expectedException mechanism still terminates
         * the current test.
         */
        try {
            $phpMinimumVersion->requireMinimumPhpVersion();
        } catch (EgressException $e) {
        }

        rewind($errorStream);

        $this->assertEquals(self::ERROR_MESSAGE, stream_get_contents($errorStream));

        fclose($errorStream);
    }

    public function testMinimumPhpVersionIsMetWritesMessageToErrorStreamIfPhpVersionIsLessThanRequiredMinimumAndThisIsACliScriptAndHaltexecutionIsTrue()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, self::CLI_SCRIPT, $errorStream);

        /**
         * The call is wrapped in a try-catch because we need to check
         * the error output after the exception is thrown, but
         * PHPUnit's @expectedException mechanism still terminates
         * the current test.
         */
        try {
            $this->assertFalse($phpMinimumVersion->minimumPhpVersionIsMet(self::HALT_EXECUTION));
        } catch (EgressException $e) {
        }

        rewind($errorStream);

        $this->assertEquals(self::ERROR_MESSAGE, stream_get_contents($errorStream));

        fclose($errorStream);
    }

    public function minimumPhpVersionIsMetDoesNotWriteMessageToErrorStreamIfPhpVersionIsLessThanRequiredMinimumAndThisIsACliScriptAndHaltexecutionIsFalse()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, self::CLI_SCRIPT, $errorStream);
        $this->expectOutputRegex('/.*/');

        /**
         * The call is wrapped in a try-catch because we need to check
         * the error output after the exception is thrown, but
         * PHPUnit's @expectedException mechanism still terminates
         * the current test.
         */
        try {
            $phpMinimumVersion->minimumPhpVersionIsMet(self::NO_HALT_EXECUTION);
        } catch (EgressException $e) {
        }

        $this->assertEquals(0, ftell($errorStream));

        fclose($errorStream);
    }

    public function testRequireMinimumPhpVersionDoesNotWriteMessageToErrorStreamIfPhpVersionIsLessThanRequiredMinimumAndThisIsNotACliScript()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, self::NOT_CLI_SCRIPT, $errorStream);
        $this->expectOutputRegex('/.*/');

        /**
         * The call is wrapped in a try-catch because we need to check
         * the error output after the exception is thrown, but
         * PHPUnit's @expectedException mechanism still terminates
         * the current test.
         */
        try {
            $phpMinimumVersion->requireMinimumPhpVersion();
        } catch (EgressException $e) {
        }

        $this->assertEquals(0, ftell($errorStream));

        fclose($errorStream);
    }

    /**
     * @dataProvider getBools
     */
    public function testMinimumPhpVersionIsMetDoesNotWriteMessageToErrorStreamIfPhpVersionIsLessThanRequiredMinimumAndThisIsNotACliScript($haltExecution)
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion(self::LESS_THAN_PHP_MINIMUM_VERSION, self::NOT_CLI_SCRIPT, $errorStream);
        $this->expectOutputRegex('/.*/');

        /**
         * The call is wrapped in a try-catch because we need to check
         * the error output after the exception is thrown, but
         * PHPUnit's @expectedException mechanism still terminates
         * the current test.
         */
        try {
            $phpMinimumVersion->minimumPhpVersionIsMet($haltExecution);
        } catch (EgressException $e) {
        }

        $this->assertEquals(0, ftell($errorStream));

        fclose($errorStream);
    }

    public function getBools()
    {
        return [
            [true, true],
            [true, false],
            [false, true],
            [false, false]
        ];
    }
}
