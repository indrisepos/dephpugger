<?php

use Dephpug\Dbgp\Server;

require_once __DIR__ . '/../Mocks/dbgpServerSocket.php';

use Dephpug\Dbgp\GlobalAttribute;

class DbgpServerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @before
     */
    protected function _before()
    {
        $this->dbgpServer = new Server();
    }

    public function testConnectionWithXDebugServer()
    {
        GlobalAttribute::$socketAccept = true;
        $success = $this->dbgpServer->eventConnectXdebugServer();
        $this->assertTrue($success);
    }

    /**
     * @expectedException        \Dephpug\Exception\ExitProgram
     * @expectedExceptionCode    1
     * @expectedExceptionMessage Client socket error
     */
    public function testSocketErrorWhenSendCommand()
    {
        GlobalAttribute::$socketWrite = false;
        $this->dbgpServer->sendCommand('Fake command');
    }

    public function testSendCommandWithoutError()
    {
        GlobalAttribute::$socketWrite = true;
        $success = $this->dbgpServer->sendCommand('Fake command');
        $this->assertTrue($success);
    }

    /**
     * @expectedException        \Dephpug\Exception\ExitProgram
     * @expectedExceptionCode    1
     * @expectedExceptionMessage Client socket error
     */
    public function testSocketErrorIfSocketReturnFalse()
    {
        GlobalAttribute::$socketRecv = false;
        $this->dbgpServer->getResponse();
    }

    public function testMessageParseWhenReceiveACompleteMessage()
    {
        $message = '511<?xml version="1.0" encoding="iso-8859-1"?>
<init xmlns="urn:debugger_protocol_v1" xmlns:xdebug="http://xdebug.org/dbgp/xdebug" fileuri="file:///path/of/project/index.php"></init>' . "\0";
        GlobalAttribute::$socketRecv = 183;
        GlobalAttribute::$buffer = $message;

        $response = $this->dbgpServer->getResponse();
        $this->assertRegexp('/^\<\?xml version=\"1.0\"/', $response);
    }
}
