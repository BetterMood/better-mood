<?php
namespace Moodle\Egress;

use Moodle\EgressInterface;

class RealEgress implements EgressInterface
{
    public function exit($status = 0) {
        exit($status);
    }
}