<?php
namespace Moodle;

class DebuggingPrinted
{
    private static $instance;
    
    private $printed = false;
    
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::instance = new self();
        }
        
        return self::$instance;
    }
    
    public function check()
    {
        return $this->printed;
    }
    
    public function setToTrue() {
        $this->printed = true;
    }
}