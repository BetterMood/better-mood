<?php
namespace Moodle;

class MoodleExitTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @expectedException Moodle\Egress\EgressException
     */
    public function testMoodleExitThrowsEgressException()
    {
        moodle_exit();
    }

    /**
     * @expectedException Moodle\Egress\EgressException
     * @expectedExceptionCode 0
     */
    public function testMoodleExitThrowsEgressExceptionWithZeroExitCodeByDefault()
    {
        moodle_exit();
    }

    /**
     * @expectedException Moodle\Egress\EgressException
     * @expectedExceptionCode 23 
     */
    public function testMoodleExitThrowsEgressExceptionWithArgAsExitCodeIfArgIsInt()
    {
        moodle_exit(23);
    }

    /**
     * @expectedException Moodle\Egress\EgressException
     * @expectedExceptionMessage Godfrey Daniel!
     */
    public function testMoodleExitThrowsEgressExceptionWithArgAsMessageIfArgIsString()
    {
        moodle_exit('Godfrey Daniel!');
    }
}
