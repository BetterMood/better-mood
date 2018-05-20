<?php
namespace Moodle\lib;

use Moodle\Egress\EgressException;

class PhpMinimumVersionTest extends \PHPUnit\Framework\TestCase
{
    const ERROR_MESSAGE = 'Moodle 3.4 or later requires at least PHP 7.0.0 (currently using version 5.6.8).' . PHP_EOL . 'Some servers may have multiple PHP versions installed, are you using the correct executable?' . PHP_EOL;

    public function testRequireMinimumPhpVersionDoesNothingIfPhpVersionIs7()
    {
        $phpMinimumVersion = new PhpMinimumVersion('7.0.0', false, STDERR);
        $this->assertNull($phpMinimumVersion->requireMinimumPhpVersion());
    }

    /**
     * @dataProvider getBools
     */
    public function testMinimumPhpVersionIsMetReturnsTrueIfPhpVersionIs7($haltExecution)
    {
        $phpMinimumVersion = new PhpMinimumVersion('7.0.0', false, STDERR);
        $this->assertTrue($phpMinimumVersion->minimumPhpVersionIsMet($haltExecution));
    }

    /**
     * @expectedException Moodle\Egress\EgressException
     */
    public function testRequireMinimumPhpVersionCallsExitIfPhpVersionIsLessThanSeven()
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, STDERR);
        $phpMinimumVersion->requireMinimumPhpVersion();
    }

    /**
     * @expectedException Moodle\Egress\EgressException
     */
    public function testPhpMinimumVersionIsMetCallsExitIfPhpVersionIsLessThanSevenAndHaltexecutionIsTrue()
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, STDERR);
        $this->assertFalse($phpMinimumVersion->minimumPhpVersionIsMet(true));
    }

    public function testPhpMinimumVersionIsMetDoesNotCallExitIfPhpVersionIsLessThanSevenAndHaltexecutionIsFalse()
    {
        $this->expectOutputRegex('/.*/');
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, STDERR);
        $this->assertFalse($phpMinimumVersion->minimumPhpVersionIsMet(false));
    }

    public function testRequireMinimumPhpVersionOutputsMessageIfPhpVersionIsLessThanSeven()
    {
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, STDERR);
        $this->expectOutputString(self::ERROR_MESSAGE);

        try {
            $phpMinimumVersion->requireMinimumPhpVersion();
        } catch (EgressException $e) {
        }
    }

    public function testMinimumPhpVersionIsMetOutputsMessageIfPhpVersionIsLessThanSevenAndHaltexecutionIsTrue()
    {
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, STDERR);
        $this->expectOutputString(self::ERROR_MESSAGE);

        try {
            $phpMinimumVersion->minimumPhpVersionIsMet(true);
        } catch (EgressException $e) {
        }
    }

    public function testMinimumPhpVersionIsMetDoesNotOutputMessageIfPhpVersionIsLessThanSevenAndHaltexecutionIsFalse()
    {
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, STDERR);
        $this->expectOutputString('');

        try {
            $phpMinimumVersion->minimumPhpVersionIsMet(false);
        } catch (EgressException $e) {
        }
    }

    public function testRequireMinimumPhpVersionWritesMessageToErrorStreamIfPhpVersionIsLessThanSevenAndThisIsACliScript()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', true, $errorStream);

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
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', true, $errorStream);

        try {
            $this->assertFalse($phpMinimumVersion->minimumPhpVersionIsMet(true));
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
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, $errorStream);
        $this->expectOutputRegex('/.*/');

        try {
            $phpMinimumVersion->minimumPhpVersionIsMet(false);
        } catch (EgressException $e) {
        }

        $this->assertEquals(0, ftell($errorStream));

        fclose($errorStream);
    }

    public function testRequireMinimumPhpVersionDoesNotWriteMessageToErrorStreamIfPhpVersionIsLessThanSevenAndThisIsNotACliScript()
    {
        $errorStream = fopen('php://memory', 'rw');
        $this->assertNotFalse($errorStream);
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, $errorStream);
        $this->expectOutputRegex('/.*/');

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
        $phpMinimumVersion = new PhpMinimumVersion('5.6.8', false, $errorStream);
        $this->expectOutputRegex('/.*/');

        try {
            $phpMinimumVersion->minimumPhpVersionIsMet($haltExecution);
        } catch (EgressException $e) {
        }

        $this->assertEquals(0, ftell($errorStream));

        fclose($errorStream);
    }

    public function getBools()
    {
        return [[true], [false]];
    }
}
