<?php
namespace Moodle\BacktraceFormatter;

use Moodle\RootDirectory;
use Moodle\BacktraceFormatterInterface;

class PlaintextBacktraceFormatterTest extends AbstractBacktraceFormatterTest
{
    protected function getFormatter(RootDirectory $rootDirectory) : BacktraceFormatterInterface
    {
        return new PlaintextBacktraceFormatter($rootDirectory);
    }

    public function testFormatReturnsExpectedValueIfBacktraceHasTwoElements()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'exception' => 'BazException',
		'line' => 23,
		'file' => self::ROOT_DIRECTORY . '/index.php'
	    ],
	    [
		'function' => 'foo',
		'line' => 23,
		'file' => self::ROOT_DIRECTORY . '/index.php'
	    ]
	];

       $this->assertEquals('* line 23 of /index.php: BazException thrown' . PHP_EOL . '* line 23 of /index.php: call to foo()' . PHP_EOL, $formatter->format($backtrace));
    }
}
