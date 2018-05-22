<?php
namespace Moodle\BacktraceFormatter;

use Moodle\BacktraceFormatterInterface;
use Moodle\RootDirectory;

class PlaintextBacktraceFormatter implements BacktraceFormatterInterface
{
    private $rootDirectory;
    
    public function __construct(RootDirectory $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }
    
    public function format(array $callers) : string
    {
        if (empty($callers)) {
            return '';
        }
    
        $from = '';
        
        foreach ($callers as $caller) {
            if (!isset($caller['line'])) {
                $caller['line'] = '?'; // probably call_user_func()
            }
            
            if (!isset($caller['file'])) {
                $caller['file'] = 'unknownfile'; // probably call_user_func()
            }
            
            $from .= '* line ' . $caller['line'] . ' of ' . str_replace($this->rootDirectory->getPathname(), '', $caller['file']);
            
            if (isset($caller['function'])) {
                $from .= ': call to ';
                
                if (isset($caller['class'])) {
                    $from .= $caller['class'] . $caller['type'];
                }
                
                $from .= $caller['function'] . '()';
            } else if (isset($caller['exception'])) {
                $from .= ': '.$caller['exception'].' thrown';
            }
            
            $from .= "\n";
        }
    
        return $from;
    }
}
