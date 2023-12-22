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

namespace Tobento\Service\Notifier\Test\Storage;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Storage\Channel;
use Tobento\Service\Notifier\Storage\StorageRepository;
use Tobento\Service\Notifier\ChannelInterface;
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
            name: 'storage',
            repository: new StorageRepository(
                storage: new  InMemoryStorage(items: []),
                table: 'notifications',
            ),
            container: new Container(),
        );
    }
    
    public function testChannel()
    {
        $channel = $this->createChannel();
        
        $this->assertInstanceof(ChannelInterface::class, $channel);
        $this->assertSame('storage', $channel->name());
    }
    
    public function testSendThrowsUndefinedAddressExceptionIfNone()
    {
        $this->expectException(UndefinedAddressException::class);
        
        $channel = $this->createChannel();
        
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
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
    
    public function testSendUsesSpecificStorageMessageSubject()
    {
        $channel = $this->createChannel();
        
        $notification = (new Notification('Subject'))
            ->addMessage('storage/database', new Message\Storage([
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
            name: 'storage',
            repository: $repository,
            container: new Container(),
        );
        
        $this->assertSame($repository, $channel->repository());
    }
}