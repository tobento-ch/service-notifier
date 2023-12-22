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

namespace Tobento\Service\Notifier\Test\Mail;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Mail\Channel;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Tobento\Service\Notifier\Exception\UndefinedMessageException;
use Tobento\Service\Mail\NullMailer;
use Tobento\Service\Mail\Message as MailMessage;
use Tobento\Service\Container\Container;

class ChannelTest extends TestCase
{
    public function testChannel()
    {
        $channel = new Channel(
            name: 'mail',
            mailer: new NullMailer('null'),
            container: new Container(),
        );
        
        $this->assertInstanceof(ChannelInterface::class, $channel);
        $this->assertSame('mail', $channel->name());
    }
    
    public function testSendThrowsUndefinedAddressExceptionIfNone()
    {
        $this->expectException(UndefinedAddressException::class);
        
        $channel = new Channel(
            name: 'mail',
            mailer: new NullMailer('null'),
            container: new Container(),
        );
        
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        
        $message = $channel->send(notification: $notification, recipient: $recipient);
    }
    
    public function testSendsMessage()
    {
        $channel = new Channel(
            name: 'mail',
            mailer: new NullMailer('null'),
            container: new Container(),
        );
        
        $notification = new Notification('Subject');
        $recipient = new Recipient(email: 'mail@example.com');
        
        $message = $channel->send(notification: $notification, recipient: $recipient);
        
        $this->assertInstanceof(MailMessage::class, $message);
        $this->assertSame('Subject', $message->getSubject());
        $this->assertSame('mail@example.com', $message->getTo()->all()[0]->email());
    }
    
    public function testSendUsesToAddressFromMailMessage()
    {
        $channel = new Channel(
            name: 'mail',
            mailer: new NullMailer('null'),
            container: new Container(),
        );
        
        $notification = (new Notification())
            ->addMessage('mail', (new MailMessage())
                ->subject('Mail Subject')
                ->to(email: 'to@example.com')
            );
        
        $recipient = new Recipient(email: 'mail@example.com');
        
        $message = $channel->send(notification: $notification, recipient: $recipient);
        
        $this->assertSame('to@example.com', $message->getTo()->all()[0]->email());
    }
    
    public function testSendUsesSpecificMailMessageSubject()
    {
        $channel = new Channel(
            name: 'mail',
            mailer: new NullMailer('null'),
            container: new Container(),
        );
        
        $notification = (new Notification('Subject'))
            ->addMessage('mail', (new MailMessage())
                ->subject('Mail Subject')
                ->to(email: 'to@example.com')
            );
        
        $recipient = new Recipient(email: 'mail@example.com');
        
        $message = $channel->send(notification: $notification, recipient: $recipient);
        
        $this->assertSame('Mail Subject', $message->getSubject());
    }
    
    public function testMailerMethod()
    {
        $mailer = new NullMailer('null');
        $channel = new Channel(
            name: 'mail',
            mailer: $mailer,
            container: new Container(),
        );
        
        $this->assertSame($mailer, $channel->mailer());
    }
}