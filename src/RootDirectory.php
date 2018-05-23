<?php
namespace Moodle;

class RootDirectory extends \SplFileInfo
{
    public function __construct()
    {
        parent::__construct(dirname(__DIR__));
    }
}