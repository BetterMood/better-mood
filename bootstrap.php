<?php
require_once __DIR__ . '/vendor/autoload.php';

if (!function_exists('moodle_exit')) {
    function moodle_exit($status = 0) {
        $exit = new Moodle\Egress\RealEgress();
        $exit->exit($status);
    }
}
