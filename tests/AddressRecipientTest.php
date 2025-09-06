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
use Tobento\Service\Notifier\AddressRecipient;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Address;
use Tobento\Service\User\Address as UserAddr;

class AddressRecipientTest extends TestCase
{
    public function testThatImplementsRecipientInterface()
    {
        $this->assertInstanceof(RecipientInterface::class, new AddressRecipient(new UserAddr(key: 'primary')));
    }
    
    public function testGetAddressForChannelMethodReturnsNullIfNotExists()
    {
        $recipient = new AddressRecipient(new UserAddr(key: 'primary'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms', notification: $notification);
        
        $this->assertSame(null, $address);
    }

    public function testGetAddressForChannelMethodReturnsEmailAddress()
    {
        $recipient = new AddressRecipient(new UserAddr(key: 'primary', email: 'foo@example.com'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'mail', notification: $notification);
        
        $this->assertInstanceof(Address\Email::class, $address);
        $this->assertSame('foo@example.com', $address->email());
    }
    
    public function testGetAddressForChannelMethodReturnsPhoneAddress()
    {
        $recipient = new AddressRecipient(new UserAddr(key: 'primary', smartphone: '554466'));
        $notification = new Notification('Subject');
        $address = $recipient->getAddressForChannel(name: 'sms', notification: $notification);
        
        $this->assertInstanceof(Address\Phone::class, $address);
        $this->assertSame('554466', $address->phone());
    }
    
    public function testGetChannelsMethod()
    {
        $recipient = new AddressRecipient(new UserAddr(key: 'primary'));
        $notification = new Notification('Subject');
        
        $this->assertSame([], $recipient->getChannels(notification: $notification));
        
        $recipient = new AddressRecipient(address: new UserAddr(key: 'primary'), channels: ['sms']);
        $this->assertSame(['sms'], $recipient->getChannels(notification: $notification));
    }
    
    public function testGetLocaleMethod()
    {
        $this->assertSame('en', (new AddressRecipient(new UserAddr(key: 'primary')))->getLocale());
        $this->assertSame('de', (new AddressRecipient(new UserAddr(key: 'primary', locale: 'de')))->getLocale());
    }
    
    public function testGetIdMethod()
    {
        $this->assertSame(0, (new AddressRecipient(new UserAddr(key: 'primary')))->getId());
        $this->assertSame(12, (new AddressRecipient(new UserAddr(key: 'primary', id: 5, userId: 12)))->getId());
    }
    
    public function testGetTypeMethod()
    {
        $this->assertSame(UserAddr::class, (new AddressRecipient(new UserAddr(key: 'primary')))->getType());
        
        $this->assertSame('custom', (new AddressRecipient(address: new UserAddr(key: 'primary'), type: 'custom'))->getType());
    }
    
    public function testAddressMethod()
    {
        $address = new UserAddr(key: 'primary');
        $this->assertTrue($address === (new AddressRecipient($address))->address());
    }
    
    public function testTypeMethod()
    {
        $this->assertSame(null, (new AddressRecipient(address: new UserAddr(key: 'primary')))->type());
        $this->assertSame('custom', (new AddressRecipient(address: new UserAddr(key: 'primary'), type: 'custom'))->type());
    }
}