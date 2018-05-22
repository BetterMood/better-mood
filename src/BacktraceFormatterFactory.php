<?php
namespace Moodle;

class BacktraceFormatterFactory
{
    private $rootDirectory;
    
    public function __construct(RootDirectory $rootDirectory)
    {
        $this->rootDirectory = $rootDirectory;
    }
    
    public function create(bool $usePlaintext)
    {
        if ($usePlaintext) {
            return new BacktraceFormatter\PlaintextBacktraceFormatter($this->rootDirectory);
        }
        
        return new BacktraceFormatter\HtmlBacktraceFormatter($this->rootDirectory);
    }
}