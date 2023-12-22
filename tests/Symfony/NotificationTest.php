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

namespace Tobento\Service\Notifier\Test\Symfony;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Symfony\Notification;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\PushNotificationInterface;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Message\SmsMessage;

class NotificationTest extends TestCase
{
    public function testThatImplementsInterfaces()
    {
        $notification = new Notification(
            recipient: new Recipient(phone: '44556677'),
            message: new SmsMessage('44556677', 'Subject'),
        );
        
        $this->assertInstanceof(SmsNotificationInterface::class, $notification);
        $this->assertInstanceof(ChatNotificationInterface::class, $notification);
        $this->assertInstanceof(PushNotificationInterface::class, $notification);
    }

    public function testMessageAndRecipientMethods()
    {
        $message = new SmsMessage('44556677', 'Subject');
        $recipient = new Recipient(phone: '44556677');
        $notification = new Notification(recipient: $recipient, message: $message);
        
        $this->assertSame($message, $notification->getMessage());
        $this->assertSame($recipient, $notification->getRecipient());
    }
    
    public function testAsSmsMessageMethod()
    {
        $message = new SmsMessage('44556677', 'Subject');
        $recipient = new Recipient(phone: '44556677');
        $notification = new Notification(recipient: $recipient, message: $message);
        
        $this->assertSame($message, $notification->asSmsMessage($recipient));
        
        $notification = new Notification(
            recipient: new NoRecipient(),
            message: new ChatMessage('Subject'),
        );
        
        $this->assertSame(null, $notification->asSmsMessage($recipient));
    }
    
    public function testAsChatMessageMethod()
    {
        $message = new ChatMessage('Subject');
        $recipient = new NoRecipient();
        $notification = new Notification(recipient: $recipient, message: $message);
        
        $this->assertSame($message, $notification->asChatMessage($recipient));
        
        $notification = new Notification(
            recipient: new NoRecipient(),
            message: new SmsMessage('44556677', 'Subject'),
        );
        
        $this->assertSame(null, $notification->asChatMessage($recipient));
    }
    
    public function testAsPushMessageMethod()
    {
        $message = new PushMessage('Subject', 'Content');
        $recipient = new NoRecipient();
        $notification = new Notification(recipient: $recipient, message: $message);
        
        $this->assertSame($message, $notification->asPushMessage($recipient));
        
        $notification = new Notification(
            recipient: new NoRecipient(),
            message: new SmsMessage('44556677', 'Subject'),
        );
        
        $this->assertSame(null, $notification->asPushMessage($recipient));
    }    
}