<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Notifier\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\ChannelMessage;
use Tobento\Service\Notifier\ChannelMessageInterface;
use Tobento\Service\Notifier\Message\Sms;

class ChannelMessageTest extends TestCase
{
    public function testThatImplementsChannelMessageInterface()
    {
        $this->assertInstanceof(ChannelMessageInterface::class, new ChannelMessage(channel: 'sms'));
    }
    
    public function testChannelMethod()
    {
        $msg = new ChannelMessage(channel: 'sms');

        $this->assertSame('sms', $msg->channel());
    }
    
    public function testMessageMethod()
    {
        $msg = new ChannelMessage(channel: 'sms');

        $this->assertSame(null, $msg->message());
        
        $sms = new Sms('Subject');
        $msg = new ChannelMessage(channel: 'sms', message: $sms);

        $this->assertSame($sms, $msg->message());
    }
    
    public function testExceptionMethod()
    {
        $msg = new ChannelMessage(channel: 'sms');

        $this->assertSame(null, $msg->exception());
        
        $exception = new \Exception();
        $msg = new ChannelMessage(channel: 'sms', exception: $exception);

        $this->assertSame($exception, $msg->exception());
    }
    
    public function testIsSuccessfulMethod()
    {
        $this->assertTrue((new ChannelMessage(channel: 'sms'))->isSuccessful());
        $this->assertFalse((new ChannelMessage(channel: 'sms', exception: new \Exception()))->isSuccessful());
    }
    
    public function testIsFailureMethod()
    {
        $this->assertFalse((new ChannelMessage(channel: 'sms'))->isFailure());
        $this->assertTrue((new ChannelMessage(channel: 'sms', exception: new \Exception()))->isFailure());
    }
}