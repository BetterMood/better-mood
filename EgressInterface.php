<?php
namespace Moodle;

interface EgressInterface
{
    /**
     * @param string|int $status
     */
    public function exit($status = 0);
}