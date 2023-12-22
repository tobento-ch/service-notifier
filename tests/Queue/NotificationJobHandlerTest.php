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

namespace Tobento\Service\Notifier\Test\Queue;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Queue\NotificationJobHandler;
use Tobento\Service\Notifier\Notifier;
use Tobento\Service\Notifier\Channels;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Queue\Job;

class NotificationJobHandlerTest extends TestCase
{
    public function testHandleJob()
    {
        $notifier = new Notifier(
            channels: new Channels(),
        );
        
        $handler = new NotificationJobHandler(
            notifier: $notifier,
        );
        
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $job = new Job(
            name: NotificationJobHandler::class,
            payload: [
                'notification' => serialize($notification),
                'recipient' => serialize($recipient),
            ],
        );
        
        $handler->handleJob($job);
        
        $this->assertTrue(true);
    }
}