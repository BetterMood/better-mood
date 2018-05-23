<?php
namespace HTMLPurifier\URIScheme;

trait AutoloadTestTrait
{
    public function testClassCanBeAutoloadedWithUnderscores()
    {
        $classUnderTest = substr(__CLASS__, 0, strlen(__CLASS__) - strlen('Test'));
        $withUnderscores = '\\' . str_replace('\\', '_', $classUnderTest);
        var_dump($classUnderTest, $withUnderscores);
        
        $this->assertInstanceOf('\\HTMLPurifier\\URLScheme', new $withUnderscores());
    }
}
