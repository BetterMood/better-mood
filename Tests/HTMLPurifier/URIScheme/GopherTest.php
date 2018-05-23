<?php
namespace HTMLPurifier\URIScheme;

require_once __DIR__ . '/AutoloadTestTrait.php';

class GopherTest extends \PHPUnit\Framework\TestCase
{
    //use AutoloadTestTrait;
    public function testClassWillAutoloadWhenWrittenWithUnderscores()
    {
        $classUnderTest = substr(__CLASS__, 0, strlen(__CLASS__) - strlen('Test'));
        $withUnderscores = '\\' . str_replace('\\', '_', $classUnderTest);
        $instance = new $withUnderscores();
        var_dump(get_class($instance));

        $this->assertInstanceOf('\\HTMLPurifier\\URLScheme', new $withUnderscores());
    }
}
