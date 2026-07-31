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
use Tobento\Service\Notifier\CompositeNotifier;
use Tobento\Service\Notifier\NotifierInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Channels;
use Tobento\Service\Notifier\NullChannel;
use Tobento\Service\Notifier\Mail;
use Tobento\Service\Notifier\Symfony;
use Tobento\Service\Notifier\ChannelMessagesInterface;
use Tobento\Service\Mail\NullMailer;
use Tobento\Service\Container\Container;

class CompositeNotifierTest extends TestCase
{
    public function testImplementsNotifierInterface()
    {
        $notifier = new CompositeNotifier();
        $this->assertInstanceOf(NotifierInterface::class, $notifier);
    }

    public function testSendWithMultipleNotifiers()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();

        // Notifier 1: NullChannel → channel "null"
        $notifierNull = new \Tobento\Service\Notifier\Notifier(
            channels: new Channels(new NullChannel())
        );

        // Notifier 2: Mail Channel → channel "mail"
        $notifierMail = new \Tobento\Service\Notifier\Notifier(
            channels: new Channels(
                new Mail\Channel(
                    name: 'mail',
                    mailer: new NullMailer('null'),
                    container: new Container(),
                )
            )
        );

        // Notifier 3: Symfony Chat Channel → channel "chat"
        $notifierChat = new \Tobento\Service\Notifier\Notifier(
            channels: new Channels(
                new Symfony\ChannelAdapter(
                    name: 'chat',
                    channel: new Symfony\PerRecipientChatChannel(),
                    container: new Container(),
                )
            )
        );

        $composite = new CompositeNotifier(
            $notifierNull,
            $notifierMail,
            $notifierChat,
        );

        $messages = $composite->send($notification, $recipient);

        // We expect 3 ChannelMessagesInterface objects
        $this->assertCount(3, $messages);

        foreach ($messages as $msg) {
            $this->assertInstanceOf(ChannelMessagesInterface::class, $msg);
            $this->assertSame($notification, $msg->notification());
            $this->assertSame($recipient, $msg->recipient());
        }

        // Check channel names to ensure correct merging
        $this->assertNotNull($messages[0]->get('null'));
        $this->assertNotNull($messages[1]->get('mail'));
        $this->assertNotNull($messages[2]->get('chat'));
    }

    public function testSendWithMultipleRecipients()
    {
        $notification = new Notification('Subject');
        $recipientA = new Recipient();
        $recipientB = new Recipient();

        $notifier = new \Tobento\Service\Notifier\Notifier(
            channels: new Channels(new NullChannel())
        );

        $composite = new CompositeNotifier($notifier);

        $messages = $composite->send($notification, $recipientA, $recipientB);

        $this->assertCount(2, $messages);
        $this->assertInstanceOf(ChannelMessagesInterface::class, $messages[0]);
        $this->assertInstanceOf(ChannelMessagesInterface::class, $messages[1]);
    }

    public function testSendWithNoNotifiers()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();

        $composite = new CompositeNotifier();

        $messages = $composite->send($notification, $recipient);

        $this->assertSame([], $messages);
    }
}