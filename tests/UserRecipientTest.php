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
use Tobento\Service\Notifier\UserRecipient;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Address;
use Tobento\Service\User\UserInterface;
use Tobento\Service\User\User;

class UserRecipientTest extends TestCase
{
    public function testThatImplementsRecipientInterface()
    {
        $this->assertInstanceof(RecipientInterface::class, new UserRecipient(new User()));
    }
    
    public function testGetAddressForChannelMethodReturnsNullIfNotExists()
    {
        $recipient = new UserRecipient(new User());
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms', notification: $notification);
        
        $this->assertSame(null, $address);
    }

    public function testGetAddressForChannelMethodReturnsEmailAddress()
    {
        $recipient = new UserRecipient(new User(email: 'foo@example.com'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'mail', notification: $notification);
        
        $this->assertInstanceof(Address\Email::class, $address);
        $this->assertSame('foo@example.com', $address->email());
    }
    
    public function testGetAddressForChannelMethodReturnsPhoneAddress()
    {
        $recipient = new UserRecipient(new User(smartphone: '554466'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms', notification: $notification);
        
        $this->assertInstanceof(Address\Phone::class, $address);
        $this->assertSame('554466', $address->phone());
    }
    
    public function testGetChannelsMethod()
    {
        $recipient = new UserRecipient(new User());
        $notification = new Notification('Subject');
        
        $this->assertSame([], $recipient->getChannels(notification: $notification));
        
        $recipient = new UserRecipient(user: new User(), channels: ['sms']);
        $this->assertSame(['sms'], $recipient->getChannels(notification: $notification));
    }
    
    public function testGetLocaleMethod()
    {
        $this->assertSame('en', (new UserRecipient(new User()))->getLocale());
        $this->assertSame('de', (new UserRecipient(new User(locale: 'de')))->getLocale());
    }
    
    public function testGetIdMethod()
    {
        $this->assertSame(0, (new UserRecipient(new User()))->getId());
        $this->assertSame(12, (new UserRecipient(new User(id: 12)))->getId());
    }
    
    public function testGetTypeMethod()
    {
        $this->assertSame(User::class, (new UserRecipient(new User()))->getType());
    }
}