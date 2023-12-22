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
use Tobento\Service\Notifier\Event\NotificationSending;
use Tobento\Service\Notifier\ChannelMessages;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;

class NotificationSendingTest extends TestCase
{
    public function testEvent()
    {
        $messages = new ChannelMessages(
            notification: new Notification('Subject'),
            recipient: new Recipient(),
        );
        
        $event = new NotificationSending(messages: $messages);
        
        $this->assertSame($messages, $event->messages());
    }
}