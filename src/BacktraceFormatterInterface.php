<?php
namespace Moodle;

interface BacktraceFormatterInterface
{
    /**
     * Formats a backtrace ready for output.
     *
     * This function does not include function arguments because they could contain sensitive information
     * not suitable to be exposed in a response.
     *
     * @param array $callers backtrace array, as returned by debug_backtrace()..
     */
    public function format(array $callers) : string;
}