<?php

namespace Command;

use Dephpug\Command\QuitCommand;

class QuitCommandTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @before
     */
    protected function _before()
    {
        $this->quitCommand = new QuitCommand();
    }

    public function testRegextAliasQ()
    {
        $regexp = $this->quitCommand->getRegexp();
        $matched = preg_match($regexp, 'q');
        $this->assertTrue((bool) $matched);
    }

    public function testRegextQuit()
    {
        $regexp = $this->quitCommand->getRegexp();
        $matched = preg_match($regexp, 'quit');
        $this->assertTrue((bool) $matched);
    }

    public function testExecutionWithAnswerFalse()
    {
        $this->quitCommand->readline = $this->getMockBuilder('\Dephpug\Readline')
            ->setMethods(['scan'])
            ->getMock();
        $this->quitCommand->readline->expects($this->once())
            ->method('scan')
            ->will($this->returnValue('n'));

        $this->quitCommand->exec();
    }

    /**
     * @expectedException        \Dephpug\Exception\ExitProgram
     * @expectedExceptionMessage Closing dephpugger
     * @expectedExceptionCode    0
     */
    public function testExecutionWithAnswerTrue()
    {
        $this->quitCommand->readline = $this->getMockBuilder('\Dephpug\Readline')
            ->setMethods(['scan'])
            ->getMock();
        $this->quitCommand->readline->expects($this->once())
            ->method('scan')
            ->will($this->returnValue('y'));

        $this->quitCommand->exec();
    }
}
