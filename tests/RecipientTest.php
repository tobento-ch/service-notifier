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
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Address;

class RecipientTest extends TestCase
{
    public function testThatImplementsRecipientInterface()
    {
        $this->assertInstanceof(RecipientInterface::class, new Recipient());
    }
    
    public function testGetAddressForChannelMethodReturnsNullIfNotExists()
    {
        $recipient = new Recipient();
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms', notification: $notification);
        
        $this->assertSame(null, $address);
    }

    public function testGetAddressForChannelMethodReturnsEmailAddress()
    {
        $recipient = new Recipient(email: 'foo@example.com');
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'mail', notification: $notification);
        
        $this->assertInstanceof(Address\Email::class, $address);
        $this->assertSame('foo@example.com', $address->email());
    }
    
    public function testGetAddressForChannelMethodReturnsEmailAddressWithEmailObj()
    {
        $recipient = new Recipient(email: new Address\Email('foo@example.com'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'mail', notification: $notification);
        
        $this->assertInstanceof(Address\Email::class, $address);
        $this->assertSame('foo@example.com', $address->email());
    }
    
    public function testGetAddressForChannelMethodReturnsAddedEmailAddress()
    {
        $recipient = new Recipient(email: new Address\Email('foo@example.com'));
        $recipient->addAddress('mail/mailchimp', new Address\Email('bar@example.com'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'mail/mailchimp', notification: $notification);
        
        $this->assertInstanceof(Address\Email::class, $address);
        $this->assertSame('bar@example.com', $address->email());
    }
    
    public function testGetAddressForChannelMethodReturnsPhoneAddress()
    {
        $recipient = new Recipient(phone: '554466');
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms', notification: $notification);
        
        $this->assertInstanceof(Address\Phone::class, $address);
        $this->assertSame('554466', $address->phone());
    }
    
    public function testGetAddressForChannelMethodReturnsPhoneAddressWithPhoneObj()
    {
        $recipient = new Recipient(phone: new Address\Phone('554466'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms', notification: $notification);
        
        $this->assertInstanceof(Address\Phone::class, $address);
        $this->assertSame('554466', $address->phone());
    }
    
    public function testGetAddressForChannelMethodReturnsAddedPhoneAddress()
    {
        $recipient = new Recipient(phone: new Address\Phone('554466'));
        $recipient->addAddress('sms/vonage', new Address\Phone('884466'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms/vonage', notification: $notification);
        
        $this->assertInstanceof(Address\Phone::class, $address);
        $this->assertSame('884466', $address->phone());
    }
    
    public function testGetAddressForChannelMethodReturnsAddedAddress()
    {
        $recipient = new Recipient();
        $recipient->addAddress('chat/slack', ['channel' => 'name']);
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'chat/slack', notification: $notification);
        
        $this->assertSame(['channel' => 'name'], $address);
    }
    
    public function testGetChannelsMethod()
    {
        $recipient = new Recipient();
        $notification = new Notification('Subject');
        
        $this->assertSame([], $recipient->getChannels(notification: $notification));
        
        $recipient = new Recipient(channels: ['sms']);
        $this->assertSame(['sms'], $recipient->getChannels(notification: $notification));
    }
    
    public function testGetLocaleMethod()
    {
        $this->assertSame('en', (new Recipient())->getLocale());
        $this->assertSame('de', (new Recipient(locale: 'de'))->getLocale());
    }
    
    public function testGetIdMethod()
    {
        $this->assertSame(null, (new Recipient())->getId());
        $this->assertSame('12', (new Recipient(id: '12'))->getId());
        $this->assertSame(12, (new Recipient(id: 12))->getId());
    }
    
    public function testGetTypeMethod()
    {
        $this->assertSame(Recipient::class, (new Recipient())->getType());
        $this->assertSame('Type', (new Recipient(type: 'Type'))->getType());
    }
}