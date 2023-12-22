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
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\Parameter\Queue;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Mail;

class NotificationTest extends TestCase
{
    public function testThatImplementsNotificationInterface()
    {
        $this->assertInstanceof(NotificationInterface::class, new Notification());
    }
    
    public function testInterfaceMethods()
    {
        $notification = (new Notification(
            channels: ['mail', 'sms'],
        ))->parameter(new Queue());

        $this->assertSame(Notification::class, $notification->getName());
        $this->assertSame(['mail', 'sms'], $notification->getChannels(new Recipient()));
        $this->assertInstanceof(ParametersInterface::class, $notification->parameters());
    }
    
    public function testGetChannelsMethodUsesRecipientChannelsIfDefined()
    {
        $notification = new Notification(channels: ['mail', 'sms']);

        $this->assertSame(['sms'], $notification->getChannels(new Recipient(channels: ['sms'])));
    }
    
    public function testSpecificContentMethods()
    {
        $notification = new Notification(subject: 'Subject', content: 'Content');

        $this->assertSame('Subject', $notification->getSubject());
        $this->assertSame('Content', $notification->getContent());
        
        $notification = (new Notification())
            ->subject('Subject')
            ->content('Content')
            ->name('Name');

        $this->assertSame('Name', $notification->getName());
        $this->assertSame('Subject', $notification->getSubject());
        $this->assertSame('Content', $notification->getContent());
    }
    
    public function testNameMethod()
    {        
        $notification = (new Notification())->name('Name');

        $this->assertSame('Name', $notification->getName());
    }
    
    public function testImplementsMessageInterfaces()
    {        
        $notification = new Notification();

        $this->assertInstanceof(Message\ToMail::class, $notification);
        $this->assertInstanceof(Message\ToSms::class, $notification);
        $this->assertInstanceof(Message\ToStorage::class, $notification);
        $this->assertInstanceof(Message\ToChat::class, $notification);
        $this->assertInstanceof(Message\ToPush::class, $notification);
    }
    
    public function testToMailMethod()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toMail(recipient: new Recipient(), channel: 'mail');
        
        $this->assertSame('Subject', $msg->getSubject());
        $this->assertSame('Content', $msg->getText());
    }
    
    public function testToMailMethodWithSpecificChannel()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toMail(recipient: new Recipient(), channel: 'mail/foo');
        
        $this->assertSame('Subject', $msg->getSubject());
        $this->assertSame('Content', $msg->getText());
    }
    
    public function testToMailMethodUsesSpecificMessage()
    {        
        $notification = (new Notification(subject: 'Subject', content: 'Content'))
            ->addMessage('mail', (new Mail\Message())
                ->subject('Lorem')
                ->text('Lorem Ipsum')
            );
        
        $msg = $notification->toMail(recipient: new Recipient(), channel: 'mail');
        
        $this->assertSame('Lorem', $msg->getSubject());
        $this->assertSame('Lorem Ipsum', $msg->getText());
    }
    
    public function testToSmsMethod()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toSms(recipient: new Recipient(), channel: 'sms');
        
        $this->assertSame('Subject', $msg->getSubject());
    }
    
    public function testToSmsMethodWithSpecificChannel()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toSms(recipient: new Recipient(), channel: 'sms/foo');
        
        $this->assertSame('Subject', $msg->getSubject());
    }
    
    public function testToSmsMethodUsesSpecificMessage()
    {        
        $notification = (new Notification(subject: 'Subject', content: 'Content'))
            ->addMessage('sms', new Message\Sms('Lorem'));
        
        $msg = $notification->toSms(recipient: new Recipient(), channel: 'sms');
        
        $this->assertSame('Lorem', $msg->getSubject());
    }
    
    public function testToStorageMethod()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toStorage(recipient: new Recipient(), channel: 'storage');
        
        $this->assertSame(['subject' => 'Subject', 'content' => 'Content'], $msg->getData());
    }
    
    public function testToStorageMethodWithSpecificChannel()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toStorage(recipient: new Recipient(), channel: 'storage/foo');
        
        $this->assertSame(['subject' => 'Subject', 'content' => 'Content'], $msg->getData());
    }
    
    public function testToStorageMethodUsesSpecificMessage()
    {        
        $notification = (new Notification(subject: 'Subject', content: 'Content'))
            ->addMessage('storage', new Message\Storage(['key' => 'value']));
        
        $msg = $notification->toStorage(recipient: new Recipient(), channel: 'storage');
        
        $this->assertSame(['key' => 'value'], $msg->getData());
    }
    
    public function testToChatMethod()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toChat(recipient: new Recipient(), channel: 'chat');
        
        $this->assertSame('Subject', $msg->getSubject());
    }
    
    public function testToChatMethodWithSpecificChannel()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toChat(recipient: new Recipient(), channel: 'chat/foo');
        
        $this->assertSame('Subject', $msg->getSubject());
    }
    
    public function testToChatMethodUsesSpecificMessage()
    {        
        $notification = (new Notification(subject: 'Subject', content: 'Content'))
            ->addMessage('chat', new Message\Chat('Lorem'));
        
        $msg = $notification->toChat(recipient: new Recipient(), channel: 'chat');
        
        $this->assertSame('Lorem', $msg->getSubject());
    }
    
    public function testToPushMethod()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toPush(recipient: new Recipient(), channel: 'push');
        
        $this->assertSame('Subject', $msg->getSubject());
    }
    
    public function testToPushMethodWithSpecificChannel()
    {        
        $notification = new Notification(subject: 'Subject', content: 'Content');
        
        $msg = $notification->toPush(recipient: new Recipient(), channel: 'push/foo');
        
        $this->assertSame('Subject', $msg->getSubject());
    }
    
    public function testToPushMethodUsesSpecificMessage()
    {        
        $notification = (new Notification(subject: 'Subject', content: 'Content'))
            ->addMessage('push', new Message\Push('Lorem'));
        
        $msg = $notification->toPush(recipient: new Recipient(), channel: 'push');
        
        $this->assertSame('Lorem', $msg->getSubject());
    }
}