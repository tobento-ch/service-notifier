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

use DateInterval;
use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\GuestRecipient;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Notification;

class GuestRecipientTest extends TestCase
{
    public function testThatImplementsRecipientInterface()
    {
        $this->assertInstanceof(RecipientInterface::class, new GuestRecipient());
    }
    
    public function testGetAddressForChannelMethodReturnsNullIfNotExists()
    {
        $recipient = new GuestRecipient();
        $notification = new Notification('Subject');

        $address = $recipient->getAddressForChannel(
            name: 'sms',
            notification: $notification
        );

        $this->assertSame(null, $address);
    }
    
    public function testGetAddressForChannelMethodReturnsId()
    {
        $recipient = new GuestRecipient(id: 'foo');
        $notification = new Notification('Subject');

        $address = $recipient->getAddressForChannel(
            name: 'browser',
            notification: $notification
        );

        $this->assertSame('foo', $address);
    }
    
    public function testGetIdMethod()
    {
        $this->assertSame(null, (new GuestRecipient())->getId());
        $this->assertSame('12', (new GuestRecipient(id: '12'))->getId());
    }
    
    public function testGetTypeMethod()
    {
        $this->assertSame(GuestRecipient::class, (new GuestRecipient())->getType());
    }
    
    public function testGetLocaleMethod()
    {
        $this->assertSame('en', (new GuestRecipient())->getLocale());
        $this->assertSame('de', (new GuestRecipient(locale: 'de'))->getLocale());
    }
    
    public function testGetChannelsMethod()
    {
        $notification = new Notification('Subject');
        
        $recipient = new GuestRecipient();
        $this->assertSame([], $recipient->getChannels(notification: $notification));
        
        $recipient = new GuestRecipient(channels: ['browser/database']);
        $this->assertSame(['browser/database'], $recipient->getChannels(notification: $notification));
    }
    
    public function testGetExpiresAfterMethod()
    {
        $recipient = new GuestRecipient();
        $this->assertSame(null, $recipient->getExpiresAfter());
        
        $recipient = new GuestRecipient(expiresAfter: 3600);
        $this->assertSame(3600, $recipient->getExpiresAfter());
        
        $interval = new DateInterval('PT2H');
        $recipient = new GuestRecipient(expiresAfter: $interval);
        $this->assertSame($interval, $recipient->getExpiresAfter());
    }
}