<?php
namespace Moodle;

use Moodle\Egress\EgressException;

class PhpMinimumVersionTest extends \PHPUnit\Framework\TestCase
{
    const ERROR_MESSAGE = 'Moodle 3.4 or later requires at least PHP 7.1.0 (currently using version 5.6.8).' . PHP_EOL . 'Some servers may have multiple PHP versions installed, are you using the correct executable?' . PHP_EOL;
    const CLI_SCRIPT = true;
    const NOT_CLI_SCRIPT = false;
    const HALT_EXECUTION = true;
    const NO_HALT_EXECUTION = false;

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
    public function testRequireMinimumPhpVersionDoesNothingIfPhpVersionIs7Point1($isCliScript)
    {
        $phpMinimumVersion = new PhpMinimumVersion('7.1.0', $isCliScript, self::$errorStream);
        $this->assertNull($phpMinimumVersion->requireMinimumPhpVersion());
    }

    /**
     * @dataProvider getBools
     */
    public function testMinimumPhpVersionIsMetReturnsTrueIfPhpVersionIs7Point1($isCliScript, $haltExecution)
    {
        $phpMinimumVersion = new PhpMinimumVersion('7.1.0', $isCliScript, self::$errorStream);
        $this->assertTrue($phpMinimumVersion->minimumPhpVersionIsMet($haltExecution));
    }

    /**
     * @expectedException \Moodle\Egress\EgressException
     * @dataProvider getBools
     */
    public function testRequireMinimumPhpVersionCallsExitIfPhpVersionIsLessThanSeven($isCliScript)
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', $isCliScript, self::$errorStream);
        $phpMinimumVersion->requireMinimumPhpVersion();
    }

    /**
     * @expectedException \Moodle\Egress\EgressException
     * @dataProvider getBools
     */
    public function testPhpMinimumVersionIsMetCallsExitIfPhpVersionIsLessThanSevenAndHaltexecutionIsTrue($isCliScript)
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', $isCliScript, self::$errorStream);
        $this->assertFalse($phpMinimumVersion->minimumPhpVersionIsMet(self::HALT_EXECUTION));
    }

    /**
     * @dataProvider getBools
     */
    public function testPhpMinimumVersionIsMetDoesNotCallExitIfPhpVersionIsLessThanSevenAndHaltexecutionIsFalse($isCliScript)
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', $isCliScript, self::$errorStream);
        $this->assertFalse($phpMinimumVersion->minimumPhpVersionIsMet(self::NO_HALT_EXECUTION));
    }

    /**
     * @expectedException \Moodle\Egress\EgressException
     */
    public function testRequireMinimumPhpVersionOutputsMessageIfPhpVersionIsLessThanSevenAndThisIsNotACliScript()
    {
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', self::NOT_CLI_SCRIPT, self::$errorStream);
        $this->expectOutputString(self::ERROR_MESSAGE);
        $phpMinimumVersion->requireMinimumPhpVersion();
    }

    /**
     * @expectedException \Moodle\Egress\EgressException
     */
    public function testMinimumPhpVersionIsMetOutputsMessageIfPhpVersionIsLessThanSevenAndThisIsNotACliScriptAndHaltexecutionIsTrue()
    {
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', self::NOT_CLI_SCRIPT, self::$errorStream);
        $this->expectOutputString(self::ERROR_MESSAGE);
        $phpMinimumVersion->minimumPhpVersionIsMet(self::HALT_EXECUTION);
    }

    public function testMinimumPhpVersionIsMetDoesNotOutputMessageIfPhpVersionIsLessThanSevenAndThisIsNotACliScriptAndHaltexecutionIsFalse()
    {
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', self::NOT_CLI_SCRIPT, self::$errorStream);
        $this->expectOutputString('');
        $phpMinimumVersion->minimumPhpVersionIsMet(self::NO_HALT_EXECUTION);
    }

    public function testRequireMinimumPhpVersionWritesMessageToErrorStreamIfPhpVersionIsLessThanSevenAndThisIsACliScript()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', self::CLI_SCRIPT, $errorStream);

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

    public function testMinimumPhpVersionIsMetWritesMessageToErrorStreamIfPhpVersionIsLessThanSevenAndThisIsACliScriptAndHaltexecutionIsTrue()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', self::CLI_SCRIPT, $errorStream);
        
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

    public function minimumPhpVersionIsMetDoesNotWriteMessageToErrorStreamIfPhpVersionIsLessThanSevenAndThisIsACliScriptAndHaltexecutionIsFalse()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', self::CLI_SCRIPT, $errorStream);
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

    public function testRequireMinimumPhpVersionDoesNotWriteMessageToErrorStreamIfPhpVersionIsLessThanSevenAndThisIsNotACliScript()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', self::NOT_CLI_SCRIPT, $errorStream);
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
    public function testMinimumPhpVersionIsMetDoesNotWriteMessageToErrorStreamIfPhpVersionIsLessThanSevenAndThisIsNotACliScript($haltExecution)
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', self::NOT_CLI_SCRIPT, $errorStream);
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
            [false,false]
        ];
    }
}
