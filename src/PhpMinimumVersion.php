<?php
namespace Moodle;

class PhpMinimumVersion
{
    private $phpVersion;
    private $isCliScript;
    private $errorStream;
    
    /**
     * @param $isCliScript
     */
    public static function create($isCliScript)
    {
        return new self(PHP_VERSION, $isCliScript, defined('STDERR') ? STDERR : STDIN);
    }
    
    /**
     * @param string $phpVersion
     * @param bool $isCliScript
     * @param mixed $errorStream
     */
    public function __construct($phpVersion, $isCliScript, $errorStream)
    {
        $this->phpVersion = $phpVersion;
        $this->isCliScript = $isCliScript;
        $this->errorStream = $errorStream;
    }
    
    /**
     * Require our minimum php version or halt execution if requirement not met.
     * @return void Execution is halted if version is not met.
     */
    public function requireMinimumPhpVersion()
    {
        // PLEASE NOTE THIS FUNCTION MUST BE COMPATIBLE WITH OLD UNSUPPORTED VERSIONS OF PHP!
        $this->minimumPhpVersionIsMet(true);
    }

    /**
     * Tests the current PHP version against Moodle's minimum requirement. When requirement
     * is not met returns false or halts execution depending $haltexecution param.
     *
     * @param bool $haltexecution Should execution be halted when requirement not met? Defaults to false.
     * @return bool returns true if requirement is met (false if not)
     */
    public function minimumPhpVersionIsMet($haltexecution = false)
    {
        // PLEASE NOTE THIS FUNCTION MUST BE COMPATIBLE WITH OLD UNSUPPORTED VERSIONS OF PHP.
        // Do not use modern php features or Moodle convenience functions (e.g. localised strings).

        $minimumversion = '7.0.0';
        $moodlerequirementchanged = '3.4';

        if (version_compare($this->phpVersion, $minimumversion) < 0) {
            if ($haltexecution) {
                $error = "Moodle ${moodlerequirementchanged} or later requires at least PHP ${minimumversion} "
                    . "(currently using version " . $this->phpVersion .").\n"
                    . "Some servers may have multiple PHP versions installed, are you using the correct executable?\n";

                if ($this->isCliScript) {
                    fwrite($this->errorStream, $error);
                } else {
                    echo $error;
                }
                moodle_exit(1);
            } else {
                return false;
            }
        }
        return true;
    }

    // DO NOT ADD EXTRA FUNCTIONS TO THIS FILE!!
    // This file must be functioning on all versions of PHP, extra functions belong elsewhere.
}
