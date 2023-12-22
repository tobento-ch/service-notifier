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
use Tobento\Service\Notifier\Notifier;
use Tobento\Service\Notifier\NotifierInterface;
use Tobento\Service\Notifier\Channels;
use Tobento\Service\Notifier\LazyChannels;
use Tobento\Service\Notifier\NullChannel;
use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\ChannelMessagesInterface;
use Tobento\Service\Notifier\Mail;
use Tobento\Service\Notifier\Parameter;
use Tobento\Service\Notifier\Event;
use Tobento\Service\Notifier\QueueHandlerInterface;
use Tobento\Service\Notifier\Exception\NotifierException;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Tobento\Service\Mail\NullMailer;
use Tobento\Service\Container\Container;
use Tobento\Service\Event\Events;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class NotifierTest extends TestCase
{
    public function testThatImplementsNotifierInterface()
    {
        $this->assertInstanceof(NotifierInterface::class, new Notifier(channels: new Channels()));
    }
    
    public function testSendWithoutAnyChannel()
    {
        $notifier = new Notifier(channels: new Channels());
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $recipientsMessages = $notifier->send(
            notification: $notification,
            recipient: $recipient,
        );
        
        $recipientMessages = $recipientsMessages[0];
        
        $this->assertInstanceof(ChannelMessagesInterface::class, $recipientMessages);
        $this->assertSame(0, $recipientMessages->count());
        $this->assertSame($notification, $recipientMessages->notification());
        $this->assertSame($recipient, $recipientMessages->recipient());
    }
    
    public function testSend()
    {
        $notifier = new Notifier(channels: new Channels(
            new NullChannel(),
        ));
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $recipientsMessages = $notifier->send(
            notification: $notification,
            recipient: $recipient,
        );
        
        $recipientMessages = $recipientsMessages[0];
        
        $this->assertInstanceof(ChannelMessagesInterface::class, $recipientMessages);
        $this->assertSame(1, $recipientMessages->count());
        $this->assertSame($notification, $recipientMessages->notification());
        $this->assertSame($recipient, $recipientMessages->recipient());
    }
    
    public function testSendMultipleRecipients()
    {
        $notifier = new Notifier(channels: new Channels(
            new NullChannel(),
        ));
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $recipientFoo = new Recipient();
        
        $recipientsMessages = $notifier->send(
            $notification,
            $recipient,
            $recipientFoo,
        );

        $this->assertSame(2, count($recipientsMessages));
    }
    
    public function testSendIgnoresUndefinedAddressAndMessageException()
    {
        $notifier = new Notifier(channels: new Channels(
            new Mail\Channel(
                name: 'mail',
                mailer: new NullMailer('null'),
                container: new Container(),
            ),
        ));
        
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $recipientsMessages = $notifier->send(
            notification: $notification,
            recipient: $recipient,
        );
        
        $recipientMessages = $recipientsMessages[0];
        
        $this->assertInstanceof(ChannelMessagesInterface::class, $recipientMessages);
        $this->assertSame(1, $recipientMessages->count());
        $this->assertSame($notification, $recipientMessages->notification());
        $this->assertSame($recipient, $recipientMessages->recipient());
        $this->assertInstanceof(UndefinedAddressException::class, $recipientMessages->get('mail')?->exception());
    }
    
    public function testThrowsNotifierExceptionIfSetupFailure()
    {
        $this->expectException(NotifierException::class);
        $this->expectExceptionMessage('failure');
        
        $channels = new LazyChannels(
            container: new Container(),
            channels: [
                'foo' => static function (string $name, ContainerInterface $c): ChannelInterface {
                    throw new \Exception('failure');
                },
            ],
        );
         
        $notifier = new Notifier(channels: $channels);
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $notifier->send(
            notification: $notification,
            recipient: $recipient,
        );
    }
    
    public function testWithQueueHandlerAndEvents()
    {
        $queueHandler = new class() implements QueueHandlerInterface
        {
            public function __construct(
                private array $handled = []
            ) {}

            public function handle(NotificationInterface $notification, RecipientInterface $recipient): void
            {
                $this->handled[] = [$notification, $recipient];
            }
            
            public function handled(): array
            {
                return $this->handled;
            }
        };
        
        $events = new Events();
        $collection = new Container();
        
        $events->listen(function(Event\NotificationQueued $event) use ($collection) {
            $collection->set('queued', $event);
        });
        
        $notifier = new Notifier(
            channels: new Channels(new NullChannel()),
            queueHandler: $queueHandler,
            eventDispatcher: $events,
        );
        
        $notification = (new Notification('Subject'))->parameter(new Parameter\Queue());
        $recipient = new Recipient();
        
        $recipientsMessages = $notifier->send(
            notification: $notification,
            recipient: $recipient,
        );
        
        $this->assertSame($notification, ($queueHandler->handled()[0][0] ?? null));
        $this->assertSame($recipient, ($queueHandler->handled()[0][1] ?? null));
        $this->assertSame([], $recipientsMessages);
        $this->assertSame($notification, $collection->get('queued')->notification());
        $this->assertSame($recipient, $collection->get('queued')->recipient());
    }
    
    public function testWithEvents()
    {
        $events = new Events();
        $collection = new Container();
        
        $events->listen(function(Event\NotificationSending $event) use ($collection) {
            $collection->set('sending', $event);
        });
        
        $events->listen(function(Event\NotificationSent $event) use ($collection) {
            $collection->set('sent', $event);
        });
        
        $notifier = new Notifier(
            channels: new Channels(new NullChannel()),
            eventDispatcher: $events,
        );
        
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $recipientsMessages = $notifier->send(
            notification: $notification,
            recipient: $recipient,
        );
        
        $this->assertSame($notification, $collection->get('sending')->messages()->notification());
        $this->assertSame($recipient, $collection->get('sending')->messages()->recipient());
        $this->assertSame($notification, $collection->get('sent')->messages()->notification());
        $this->assertSame($recipient, $collection->get('sent')->messages()->recipient());
    }
}