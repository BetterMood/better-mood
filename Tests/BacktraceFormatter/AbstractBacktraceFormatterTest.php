<?php
namespace Moodle\BacktraceFormatter;

use Moodle\BacktraceFormatterInterface;
use Moodle\RootDirectory;

abstract class AbstractBacktraceFormatterTest extends \PHPUnit\Framework\TestCase
{
    const ROOT_DIRECTORY = '/path/to/moodle';

    protected $rootDirectory;

    abstract protected function getFormatter(RootDirectory $rootDirectory) : BacktraceFormatterInterface;

    public function setUp()
    {
	$this->rootDirectory = $this->createMock('Moodle\RootDirectory', ['getPathname']);
	$this->rootDirectory
	    ->expects($this->any())
	    ->method('getPathname')
	    ->will($this->returnValue(self::ROOT_DIRECTORY));
    }

    public function testFormatReturnsAnEmptyStringIfBacktraceIsEmpty()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [];

	$this->assertEquals('', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAFunctionCallBacktrace()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'function' => 'foo',
		'line' => 23,
		'file' => self::ROOT_DIRECTORY . '/index.php'
	    ]
	];

       $this->assertRegExp('/line 23 of \/index\.php: call to foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAMethodCallBacktrace()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'function' => 'foo',
		'line' => 23,
		'file' => self::ROOT_DIRECTORY . '/index.php',
		'class' => 'Bar',
		'type' => '->'
	    ]
	];

       $this->assertRegExp('/line 23 of \/index\.php: call to Bar->foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAStaticMethodCallBacktrace()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'function' => 'foo',
		'line' => 23,
		'file' => self::ROOT_DIRECTORY . '/index.php',
		'class' => 'Bar',
		'type' => '::'
	    ]
	];

       $this->assertRegExp('/line 23 of \/index\.php: call to Bar::foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAnExceptionBacktrace()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'exception' => 'BazException',
		'line' => 23,
		'file' => self::ROOT_DIRECTORY . '/index.php'
	    ]
	];

       $this->assertRegExp('/line 23 of \/index\.php: BazException thrown/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAFunctionCallBacktraceWithNoLine()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'function' => 'foo',
		'file' => self::ROOT_DIRECTORY . '/index.php'
	    ]
	];

       $this->assertRegExp('/line \? of \/index\.php: call to foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAMethodCallBacktraceWithNoLine()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'function' => 'foo',
		'file' => self::ROOT_DIRECTORY . '/index.php',
		'class' => 'Bar',
		'type' => '->'
	    ]
	];

       $this->assertRegExp('/line \? of \/index\.php: call to Bar->foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAStaticMethodCallBacktraceWithNoLine()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'function' => 'foo',
		'file' => self::ROOT_DIRECTORY . '/index.php',
		'class' => 'Bar',
		'type' => '::'
	    ]
	];

       $this->assertRegExp('/line \? of \/index\.php: call to Bar::foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAnExceptionBacktraceWithNoLine()
    {
	$formatter = $this->getFormatter($this->rootDirectory);
	$backtrace = [
	    [
		'exception' => 'BazException',
		'file' => self::ROOT_DIRECTORY . '/index.php'
            ]
        ];

       $this->assertRegExp('/line \? of \/index\.php: BazException thrown/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAFunctionCallBacktraceWithNoFile()
    {
        $formatter = $this->getFormatter($this->rootDirectory);
        $backtrace = [
            [
                'function' => 'foo',
                'line' => 23
            ]
        ];

       $this->assertRegExp('/line 23 of unknownfile: call to foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAMethodCallBacktraceWithNoFile()
    {
        $formatter = $this->getFormatter($this->rootDirectory);
        $backtrace = [
            [
                'function' => 'foo',
                'line' => 23,
                'class' => 'Bar',
                'type' => '->'
            ]
        ];

       $this->assertRegExp('/line 23 of unknownfile: call to Bar->foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAStaticMethodCallBacktraceWithNoFile()
    {
        $formatter = $this->getFormatter($this->rootDirectory);
        $backtrace = [
            [
                'function' => 'foo',
                'line' => 23,
                'class' => 'Bar',
                'type' => '::'
            ]
        ];

       $this->assertRegExp('/line 23 of unknownfile: call to Bar::foo\(\)/', $formatter->format($backtrace));
    }

    public function testFormatReturnsExpectedPatternIfBacktraceIsAnExceptionBacktraceWithNoFile()
    {
        $formatter = $this->getFormatter($this->rootDirectory);
        $backtrace = [
            [
                'exception' => 'BazException',
                'line' => 23
            ]
        ];

       $this->assertRegExp('/line 23 of unknownfile: BazException thrown/', $formatter->format($backtrace));
    }
}
