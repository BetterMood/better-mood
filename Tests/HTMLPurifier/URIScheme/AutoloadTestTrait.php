<?php
namespace HTMLPurifier\URLScheme;

trait AutoloadTestTrait
{
    public function testClassCanBeAutoloadedWithUnderscores()
    {
        $classUnderTest = substr(__CLASS__, 0, strlen(__CLASS__) - strlen('Test'));
        $withUnderscores = str_replace('\', '_', $classUnderTest);
        
        $this->assertInstanceOf('HTMLPurifier\URLScheme', new $withUnderscores());
    }
}