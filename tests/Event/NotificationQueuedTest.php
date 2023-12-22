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

namespace Tobento\Service\Notifier\Test\Event;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Event\NotificationQueued;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;

class NotificationQueuedTest extends TestCase
{
    public function testEvent()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $event = new NotificationQueued(
            notification: $notification,
            recipient: $recipient,
        );
        
        $this->assertSame($notification, $event->notification());
        $this->assertSame($recipient, $event->recipient());
    }
}