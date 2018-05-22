<?php
namespace Moodle\Egress;

use Moodle\EgressInterface;

class TestEgress implements EgressInterface
{
    public function exit($status = 0) {
        /**
         * PHP's exit builtin behaves differently depending on whether its
         * argument is an int or a string.
         */
        if (is_int($status)) {
            throw new EgressException('', $status);
        }
        
        throw new EgressException((string) $status);
    }
}