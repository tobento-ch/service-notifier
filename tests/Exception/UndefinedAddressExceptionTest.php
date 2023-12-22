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

namespace Tobento\Service\Notifier\Test\Exception;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Tobento\Service\Notifier\Exception\ChannelException;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;

class UndefinedAddressExceptionTest extends TestCase
{
    public function testException()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $e = new UndefinedAddressException(
            channel: 'name',
            notification: $notification,
            recipient: $recipient,
        );
        
        $this->assertInstanceof(ChannelException::class, $e);
        $this->assertSame('name', $e->channel());
        $this->assertSame($notification, $e->notification());
        $this->assertSame($recipient, $e->recipient());
        $this->assertSame(
            'Notification Tobento\Service\Notifier\Notification has no address for the channel name defined',
            $e->getMessage()
        );
        
        $this->assertSame(
            'Custom',
            (new UndefinedAddressException(
                channel: 'name',
                notification: $notification,
                recipient: $recipient,
                message: 'Custom',
            ))->getMessage()
        );
    }
}