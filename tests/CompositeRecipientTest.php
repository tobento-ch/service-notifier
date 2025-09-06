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
use Tobento\Service\Notifier\CompositeRecipient;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Address;

class CompositeRecipientTest extends TestCase
{
    public function testThatImplementsRecipientInterface()
    {
        $this->assertInstanceof(RecipientInterface::class, new CompositeRecipient());
    }
    
    public function testGetAddressForChannelMethodReturnsNullIfNotExists()
    {
        $recipient = new CompositeRecipient();
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms', notification: $notification);
        
        $this->assertSame(null, $address);
    }

    public function testGetAddressForChannelMethodReturnsFirstFoundAddress()
    {
        $recipient = new CompositeRecipient(
            new Recipient(email: 'foo@example.com'),
            new Recipient(email: 'bar@example.com'),
        );
        
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'mail', notification: $notification);
        
        $this->assertSame('foo@example.com', $address->email());
    }
    
    public function testGetChannelsMethod()
    {
        $recipient = new CompositeRecipient();
        $notification = new Notification('Subject');
        
        $this->assertSame([], $recipient->getChannels(notification: $notification));
        
        $recipient = new CompositeRecipient(
            new Recipient(email: 'foo@example.com', channels: ['sms']),
            new Recipient(email: 'bar@example.com'),
        );
        
        $this->assertSame(['sms'], $recipient->getChannels(notification: $notification));
    }
    
    public function testGetLocaleMethod()
    {
        $recipient = new CompositeRecipient();
        
        $this->assertSame('en', $recipient->getLocale());
        
        $recipient = new CompositeRecipient(
            new Recipient(email: 'foo@example.com', locale: 'de'),
            new Recipient(email: 'bar@example.com'),
        );
        
        $this->assertSame('de', $recipient->getLocale());
    }
    
    public function testGetIdMethod()
    {
        $recipient = new CompositeRecipient();
        
        $this->assertSame(null, $recipient->getId());
        
        $recipient = new CompositeRecipient(
            new Recipient(email: 'foo@example.com'),
            new Recipient(email: 'bar@example.com', id: 5),
        );
        
        $this->assertSame(5, $recipient->getId());
    }
    
    public function testGetTypeMethod()
    {
        $recipient = new CompositeRecipient();
        
        $this->assertSame(CompositeRecipient::class, $recipient->getType());
        
        $recipient = new CompositeRecipient(
            new Recipient(email: 'foo@example.com'),
            new Recipient(email: 'bar@example.com', type: 'bar'),
        );
        
        $this->assertSame(Recipient::class, $recipient->getType());
    }
    
    public function testRecipientsMethod()
    {
        $recipient = new CompositeRecipient(
            new Recipient(email: 'foo@example.com'),
            new Recipient(email: 'bar@example.com', type: 'bar'),
        );
        
        $this->assertSame(2, count($recipient->recipients()));
    }
}