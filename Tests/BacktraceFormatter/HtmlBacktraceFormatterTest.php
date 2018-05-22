<?php
namespace Moodle\BacktraceFormatter;

use Moodle\RootDirectory;
use Moodle\BacktraceFormatterInterface;

class HtmlBacktraceFormatterTest extends AbstractBacktraceFormatterTest
{
    protected function getFormatter(RootDirectory $rootDirectory) : BacktraceFormatterInterface
    {
        return new HtmlBacktraceFormatter($rootDirectory);
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

       $this->assertEquals('<ul style="text-align: left" data-rel="backtrace"><li>line 23 of /index.php: BazException thrown</li><li>line 23 of /index.php: call to foo()</li></ul>', $formatter->format($backtrace));
    }
}
