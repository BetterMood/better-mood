<?php
namespace Moodle;

class BacktraceFormatter
{
    private $rootDirectory;
    
    public static function create()
    {
        return new self(new RootDirectory());
    }
    
    public function __construct(RootDirectory $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }

    /**
     * Formats a backtrace ready for output.
     *
     * This function does not include function arguments because they could contain sensitive information
     * not suitable to be exposed in a response.
     *
     * @param array $callers backtrace array, as returned by debug_backtrace().
     * @param boolean $plaintext if false, generates HTML, if true generates plain text.
     * @return string formatted backtrace, ready for output.
     */
    function format($callers, $plaintext = false) {
        if (empty($callers)) {
            return '';
        }
    
        $from = $plaintext ? '' : '<ul style="text-align: left" data-rel="backtrace">';
        foreach ($callers as $caller) {
            if (!isset($caller['line'])) {
                $caller['line'] = '?'; // probably call_user_func()
            }
            if (!isset($caller['file'])) {
                $caller['file'] = 'unknownfile'; // probably call_user_func()
            }
            $from .= $plaintext ? '* ' : '<li>';
            $from .= 'line ' . $caller['line'] . ' of ' . str_replace($this->rootDirectory->getPathname(), '', $caller['file']);
            if (isset($caller['function'])) {
                $from .= ': call to ';
                if (isset($caller['class'])) {
                    $from .= $caller['class'] . $caller['type'];
                }
                $from .= $caller['function'] . '()';
            } else if (isset($caller['exception'])) {
                $from .= ': '.$caller['exception'].' thrown';
            }
            $from .= $plaintext ? "\n" : '</li>';
        }
        $from .= $plaintext ? '' : '</ul>';
    
        return $from;
    }
}