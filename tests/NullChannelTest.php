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
use Tobento\Service\Notifier\NullChannel;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\Message\NullMessage;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;

class NullChannelTest extends TestCase
{
    public function testThatImplementsChannelInterface()
    {
        $this->assertInstanceof(ChannelInterface::class, new NullChannel());
    }
    
    public function testNameMethod()
    {
        $this->assertSame('null', (new NullChannel())->name());
        $this->assertSame('foo', (new NullChannel(name: 'foo'))->name());
    }
    
    public function testSendMethod()
    {
        $msg = (new NullChannel())->send(
            notification: new Notification(),
            recipient: new Recipient(),
        );
        
        $this->assertInstanceof(NullMessage::class, $msg);
    }
}