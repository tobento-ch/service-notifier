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

namespace Tobento\Service\Notifier\Test\Browser;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tobento\Service\Clock\FrozenClock;
use Tobento\Service\Notifier\Browser\Channel;
use Tobento\Service\Notifier\Browser\StorageRepository;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\GuestRecipient;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Tobento\Service\Notifier\Exception\UndefinedMessageException;
use Tobento\Service\Storage\InMemoryStorage;
use Tobento\Service\Storage\ItemInterface;
use Tobento\Service\Container\Container;

class ChannelTest extends TestCase
{
    protected function createChannel()
    {
        return new Channel(
            name: 'browser',
            repository: new StorageRepository(
                storage: new  InMemoryStorage(items: []),
                table: 'notifications',
            ),
            clock: new FrozenClock(),
            container: new Container(),
        );
    }
    
    public function testChannel()
    {
        $channel = $this->createChannel();
        
        $this->assertInstanceof(ChannelInterface::class, $channel);
        $this->assertSame('browser', $channel->name());
    }
    
    public function testSendThrowsUndefinedAddressExceptionIfNone()
    {
        $this->expectException(UndefinedAddressException::class);
        
        $channel = $this->createChannel();
        
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $message = $channel->send(notification: $notification, recipient: $recipient);
    }
    
    public function testSendThrowsUndefinedAddressExceptionIfEmpty()
    {
        $this->expectException(UndefinedAddressException::class);
        
        $channel = $this->createChannel();
        
        $notification = new Notification('Subject');
        $recipient = new Recipient(id: 0);
        
        $message = $channel->send(notification: $notification, recipient: $recipient);
    }
    
    public function testSendsMessage()
    {
        $channel = $this->createChannel();
        
        $notification = new Notification('Subject');
        $recipient = new Recipient(id: 5);
        
        $this->assertSame(0, $channel->repository()->count());
        
        $message = $channel->send(notification: $notification, recipient: $recipient);
        
        $this->assertSame(1, $channel->repository()->count());
        $this->assertInstanceof(ItemInterface::class, $message);
        $this->assertSame(Notification::class, $message->get('name'));
        $this->assertEquals(5, $message->get('recipient_id'));
        $this->assertSame(Recipient::class, $message->get('recipient_type'));
        $this->assertSame(['subject' => 'Subject', 'content' => ''], $message->get('data'));
    }
    
    public function testSendUsesSpecificBrowserMessageSubject()
    {
        $channel = $this->createChannel();
        
        $notification = new Notification('Subject')
            ->addMessage('browser/database', new Message\Browser([
                'foo' => 'bar',
            ]));
        
        $recipient = new Recipient(id: 5);
        
        $message = $channel->send(notification: $notification, recipient: $recipient);
        
        $this->assertSame(['foo' => 'bar'], $message->get('data'));
    }
    
    public function testRepositoryMethod()
    {
        $repository = new StorageRepository(
            storage: new  InMemoryStorage(items: []),
            table: 'notifications',
        );
        
        $channel = new Channel(
            name: 'browser',
            repository: $repository,
            clock: new FrozenClock(),
            container: new Container(),
        );
        
        $this->assertSame($repository, $channel->repository());
    }
    
    public function testGuestRecipientDoesNotRequireId()
    {
        $channel = $this->createChannel();

        $notification = new Notification('Subject');
        $recipient = new GuestRecipient(id: null);

        $message = $channel->send(notification: $notification, recipient: $recipient);

        $this->assertSame(1, $channel->repository()->count());
        $this->assertNull($message->get('recipient_id'));
    }
    
    public function testGuestRecipientExpiresAfterInt()
    {
        $clock = new FrozenClock(new DateTimeImmutable('2026-01-01 12:00:00'));

        $channel = new Channel(
            name: 'browser',
            repository: new StorageRepository(
                storage: new InMemoryStorage(items: []),
                table: 'notifications',
            ),
            clock: $clock,
            container: new Container(),
        );

        $notification = new Notification('Subject');
        $recipient = new GuestRecipient(
            id: null,
            expiresAfter: 3600, // 1 hour
        );

        $message = $channel->send(notification: $notification, recipient: $recipient);

        $this->assertSame('2026-01-01 13:00:00', $message->get('expires_at'));
    }
    
    public function testGuestRecipientExpiresAfterDateInterval()
    {
        $clock = new FrozenClock(new DateTimeImmutable('2026-01-01 12:00:00'));

        $channel = new Channel(
            name: 'browser',
            repository: new StorageRepository(
                storage: new InMemoryStorage(items: []),
                table: 'notifications',
            ),
            clock: $clock,
            container: new Container(),
        );

        $notification = new Notification('Subject');
        $recipient = new GuestRecipient(
            id: null,
            expiresAfter: new \DateInterval('PT2H'),
        );

        $message = $channel->send(notification: $notification, recipient: $recipient);

        $this->assertSame('2026-01-01 14:00:00', $message->get('expires_at'));
    }
    
    public function testGuestRecipientWithoutExpirationStoresNull()
    {
        $channel = $this->createChannel();

        $notification = new Notification('Subject');
        $recipient = new GuestRecipient(
            id: null,
            expiresAfter: null,
        );

        $message = $channel->send(notification: $notification, recipient: $recipient);

        $this->assertNull($message->get('expires_at'));
    }
}