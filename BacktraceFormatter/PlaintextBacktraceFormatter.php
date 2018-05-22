<?php
namespace Moodle\BacktraceFormatter;

use Moodle\BacktraceFormatterInterface;

class PlaintextBacktraceFormatter implements BacktraceFormatterInterface
{
    public function format($callers) 
        // do not use $CFG->dirroot because it might not be available in destructors
        $dirroot = dirname(__DIR__);
    
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
            
            $from .= '* line ' . $caller['line'] . ' of ' . str_replace($dirroot, '', $caller['file']);
            
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