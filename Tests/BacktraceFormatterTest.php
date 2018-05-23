<?php
namespace Moodle;

class BacktraceFormatterTest extends \PHPUnit\Framework\TestCase
{
    const ROOT_DIRECTORY = '/path/to/moodle';
    const PLAINTEXT = true;
    const NO_PLAINTEXT = false;

    private $formatter;

    public function setUp()
    {
        $rootDirectory = $this->createMock('Moodle\RootDirectory', ['getPathname']);
        $rootDirectory
            ->expects($this->any())
            ->method('getPathname')
            ->will($this->returnValue(self::ROOT_DIRECTORY));
            $this->formatter = new BacktraceFormatter($rootDirectory);
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsAnEmptyStringIfBacktraceIsEmpty($plaintext)
    {
        $backtrace = [];

        $this->assertEquals('', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAFunctionCallBacktrace($plaintext)
    {
        $backtrace = [
        [
        'function' => 'foo',
        'line' => 23,
        'file' => self::ROOT_DIRECTORY . '/index.php'
        ]
    ];

       $this->assertRegExp('/line 23 of \/index\.php: call to foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAMethodCallBacktrace($plaintext)
    {
    $backtrace = [
        [
        'function' => 'foo',
        'line' => 23,
        'file' => self::ROOT_DIRECTORY . '/index.php',
        'class' => 'Bar',
        'type' => '->'
        ]
    ];

       $this->assertRegExp('/line 23 of \/index\.php: call to Bar->foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAStaticMethodCallBacktrace($plaintext)
    {
        $backtrace = [
            [
            'function' => 'foo',
            'line' => 23,
            'file' => self::ROOT_DIRECTORY . '/index.php',
            'class' => 'Bar',
            'type' => '::'
            ]
        ];

        $this->assertRegExp('/line 23 of \/index\.php: call to Bar::foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAnExceptionBacktrace($plaintext)
    {
        $backtrace = [
            [
            'exception' => 'BazException',
            'line' => 23,
            'file' => self::ROOT_DIRECTORY . '/index.php'
            ]
        ];

       $this->assertRegExp('/line 23 of \/index\.php: BazException thrown/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAFunctionCallBacktraceWithNoLine($plaintext)
    {
        $backtrace = [
            [
            'function' => 'foo',
            'file' => self::ROOT_DIRECTORY . '/index.php'
            ]
        ];

       $this->assertRegExp('/line \? of \/index\.php: call to foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAMethodCallBacktraceWithNoLine($plaintext)
    {
        $backtrace = [
            [
            'function' => 'foo',
            'file' => self::ROOT_DIRECTORY . '/index.php',
            'class' => 'Bar',
            'type' => '->'
            ]
        ];

       $this->assertRegExp('/line \? of \/index\.php: call to Bar->foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAStaticMethodCallBacktraceWithNoLine($plaintext)
    {
        $backtrace = [
            [
            'function' => 'foo',
            'file' => self::ROOT_DIRECTORY . '/index.php',
            'class' => 'Bar',
            'type' => '::'
            ]
        ];

        $this->assertRegExp('/line \? of \/index\.php: call to Bar::foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAnExceptionBacktraceWithNoLine($plaintext)
    {
        $backtrace = [
            [
                'exception' => 'BazException',
                'file' => self::ROOT_DIRECTORY . '/index.php'
            ]
        ];

        $this->assertRegExp('/line \? of \/index\.php: BazException thrown/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAFunctionCallBacktraceWithNoFile($plaintext)
    {
        $backtrace = [
            [
                'function' => 'foo',
                'line' => 23
            ]
        ];

        $this->assertRegExp('/line 23 of unknownfile: call to foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAMethodCallBacktraceWithNoFile($plaintext)
    {
        $backtrace = [
            [
                'function' => 'foo',
                'line' => 23,
                'class' => 'Bar',
                'type' => '->'
            ]
        ];

       $this->assertRegExp('/line 23 of unknownfile: call to Bar->foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAStaticMethodCallBacktraceWithNoFile($plaintext)
    {
        $backtrace = [
            [
                'function' => 'foo',
                'line' => 23,
                'class' => 'Bar',
                'type' => '::'
            ]
        ];

        $this->assertRegExp('/line 23 of unknownfile: call to Bar::foo\(\)/', $this->formatter->format($backtrace, $plaintext));
    }

    /**
     * @dataProvider getPlaintext
     */
    public function testFormatReturnsExpectedPatternIfBacktraceIsAnExceptionBacktraceWithNoFile($plaintext)
    {
        $backtrace = [
            [
                'exception' => 'BazException',
                'line' => 23
            ]
        ];

        $this->assertRegExp('/line 23 of unknownfile: BazException thrown/', $this->formatter->format($backtrace, $plaintext));
    }
    
    public function getPlaintext()
    {
        return [[self::PLAINTEXT], [self::NO_PLAINTEXT]];
    }
    
    public function testFormatReturnsExpectedValueIfBacktraceHasTwoElementsAndPlaintextIsFalse()
    {
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

        $this->assertEquals('<ul style="text-align: left" data-rel="backtrace"><li>line 23 of /index.php: BazException thrown</li><li>line 23 of /index.php: call to foo()</li></ul>', $this->formatter->format($backtrace, self::NO_PLAINTEXT));
    }
    
    public function testFormatReturnsExpectedValueIfBacktraceHasTwoElementsAndPlaintextIsTrue()
    {
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

        $this->assertEquals('* line 23 of /index.php: BazException thrown' . PHP_EOL . '* line 23 of /index.php: call to foo()' . PHP_EOL, $this->formatter->format($backtrace, self::PLAINTEXT));
    }
}
