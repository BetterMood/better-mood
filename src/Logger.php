<?php
namespace Moodle;

class Logger
{
    const DEBUG_NONE = 0;
    const DEBUG_MINIMAL = E_ERROR | E_PARSE;
    const DEBUG_NORMAL = E_ERROR | E_PARSE | E_WARNING | E_NOTICE;
    const DEBUG_ALL = E_ALL & ~E_STRICT;
    const DEBUG_DEVELOPER = E_ALL | E_STRICT;

    private $backtraceFormatter;
    private $config;
    private $user;
    private $isCliScript;
    private $shouldSuppressDebugDisplay;
    private $isPhpUnitTest;
    
    public static function create()
    {
        global $CFG, $USER;

        return new self(
            BacktraceFormatter::create(),
            DebuggingPrinted::getInstance(),
            $CFG,
            $USER,
            CLI_SCRIPT,
            NO_DEBUG_DISPLAY,
            PHPUNIT_TEST
        );
    }
    
    public function __construct(
        BacktraceFormatter $backtraceFormatter,
        DebuggingPrinted $debuggingPrinted,
        $config,
        $user,
        $isCliScript,
        $shouldSuppressDebugDisplay,
        $isPhpUnitTest
    ) {
        $this->backtraceFormatter = $backtraceFormatter;
        $this->debuggingPrinted = $debuggingPrinted;
        $this->config = $config;
        $this->user = $user;
        $this->isCliScript = $isCliScript;
        $this->shouldSuppressDebugDisplay = $shouldSuppressDebugDisplay;
        $this->isPhpUnitTest = $isPhpUnitTest;
    }
    
    /**
     * Standard Debugging Function
     *
     * Returns true if the current site debugging settings are equal or above specified level.
     * If passed a parameter it will emit a debugging notice similar to trigger_error(). The
     * routing of notices is controlled by $this->config->debugdisplay
     * eg use like this:
     *
     * 1)  debug('a normal debug notice');
     * 2)  debug('something really picky', \Moodle\Logger::DEBUG_ALL);
     * 3)  debug('annoying debug message only for developers', \Moodle\Logger::DEBUG_DEVELOPER);
     * 4)  if (debug()) { perform extra debugging operations (do not use print or echo) }
     *
     * In code blocks controlled by debugging() (such as example 4)
     * any output should be routed via debugging() itself, or the lower-level
     * trigger_error() or error_log(). Using echo or print will break XHTML
     * JS and HTTP headers.
     *
     * It is also possible to define NO_DEBUG_DISPLAY which redirects the message to error_log.
     *
     * @param string $message a message to print
     * @param int $level the level at which this debugging statement should show
     * @param array $backtrace use different backtrace
     * @return bool
     */
    public function debug($message = '', $level = self::DEBUG_NORMAL, $backtrace = null) {
        $forcedebug = false;
        
        if (!empty($this->config->debugusers) && $this->user) {
            $debugusers = explode(',', $this->config->debugusers);
            $forcedebug = in_array($this->user->id, $debugusers);
        }
    
        if (!$forcedebug and (empty($this->config->debug) || ($this->config->debug != -1 and $this->config->debug < $level))) {
            return false;
        }
    
        if (!isset($this->config->debugdisplay)) {
            $this->config->debugdisplay = ini_get_bool('display_errors');
        }
    
        if ($message) {
            if (!$backtrace) {
                $backtrace = debug_backtrace();
            }
            
            $from = $this->backtraceFormatter->format($backtrace, $this->isCliScript || $this->shouldSuppressDebugDisplay);
            
            if ($this->isPhpUnitTest) {
                if (PhpUnit\Util::debugging_triggered($message, $level, $from)) {
                    // We are inside test, the debug message was logged.
                    return true;
                }
            }
    
            if ($this->shouldSuppressDebugDisplay) {
                // Script does not want any errors or debugging in output,
                // we send the info to error log instead.
                error_log('Debugging: ' . $message . ' in '. PHP_EOL . $from);
    
            } else if ($forcedebug or $this->config->debugdisplay) {
                if (!$this->debuggingPrinted->check()) {
                    $this->debuggingPrinted->setToTrue(); // Indicates we have printed something.
                }
                if ($this->isCliScript) {
                    echo "++ $message ++\n$from";
                } else {
                    echo '<div class="notifytiny debuggingmessage" data-rel="debugging">' , $message , $from , '</div>';
                }
    
            } else {
                trigger_error($message . $from, E_USER_NOTICE);
            }
        }
        return true;
    }
}
